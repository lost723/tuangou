<?php

namespace App\Http\Controllers\Customer;

use App\Events\PaySuccessEvent;
use App\Models\Customer\Order;
use App\Models\Customer\OrderPromotion;
use App\Models\Customer\PayLog;
use function EasyWeChat\Kernel\Support\generate_sign;
use Illuminate\Http\Request;
use App\Http\Controllers\BasePaymentController;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\Customer\Order as OrderResource;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Common\NoticeController;
use App\Models\Auth\Customer;



class PaymentController extends BasePaymentController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth', ['except' =>  ['notify']]);
    }

    # 检测订单是否超时 超时更新订单状态
    public function checkTimeOut($id)
    {
        if(!($order = Order::checkOrder($id))) {
            try{
                DB::beginTransaction();
                Order::cancelCasecadeOrder($id); # 更新订单状态
                DB::commit();
            }
            catch (\Exception $exception) {
                DB::rollback();
                throw new \Exception($exception->getMessage());
            }
            throw new \Exception('订单不存在或已超时！');
        }
        return $order;
    }

    # 支付订单
    # profit_sharing  字段 值'Y' 分账字段
    public function Pay(Request $request)
    {   # todo 暂时没有分账权限
        try{
            $id = $request->post('id');
            $order = $this->checkTimeOut($id);
            $customer = auth()->user();
            $data = [];
            $data['body']               = '团购';
            $data['out_trade_no']       = $order->trade_no;
            $data['total_fee']          = $order->total;
            $data['sub_openid']         = $customer->openid;
            $data['trade_type']         = 'JSAPI';
//            $data['profit_sharing']     = 'Y';
            $result = $this->payment->order->unify($data);
            dump($result);die;
            if($result['return_code'] <> 'SUCCESS' ||$result['result_code'] <> 'SUCCESS') {
                throw new \Exception($result['err_code_des']);
            }
            $response = [
                'appId'     =>  $this->config['sub_appid'],
                'timeStamp' =>  ''.time(),
                'nonceStr'  =>  $result['nonce_str'],
                'signType'  =>  'MD5',
                'package'   =>  'prepay_id='.$result['prepay_id'],
            ];
            # todo 发起支付

//            $notice = new NoticeController();
//            $customer = Customer::find(28);
//            $templateid = 'eVWV28sTr5Ht_J3Scm_PFFRFiUQ8DRWTR-cEnugRruE';
//            $page = 'pages/home/home/home';
////            $formid = 'prepay_id=wx2611013052570893968d0fb22410531545';
//            $formid = $result['prepay_id'];
//            Log::info($formid);
//            $oms = OrderPromotion::getOrderPromotionDetail(5);
//            $data = [
//                'keyword1' => date('Y-m-d H:i:s', $oms->deliveryday),
//                'keyword2' => date('Y-m-d H:i:s', $oms->createtime),
//                'keyword3' => $oms->title,
//                'keyword4' => $oms->ordersn,
//                'keyword5' => $oms->checkcode,
//                'keyword6' => '已发货',
//            ];
//            $result = $notice->sendTemplateMessage($customer->openid, $templateid, $page, $formid, $data);
//            Log::info(print_r($result,1));

            # 日志--发起支付
            $log = [
                'customerid'    =>  $customer->id,
                'trade_no'      =>  $order->trade_no,
                'fee'           =>  $order->total,
                'status'        =>  Order::Unpaid,
                'note'          =>  '发起支付'
            ];
            PayLog::crateLog($log);
            # ---end---
            $response['paySign'] = generate_sign($response, $this->config['key'], 'md5');
            return $this->okWithResource($response);
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }

    public function paySuccess(Request $request)
    {
        try{
            $id = $request->post('id');
            $order = $this->checkTimeOut($id);
            $result = $this->payment->order->queryByOutTradeNumber($order->trade_no);//dump($result);die;
            if($result['return_code'] <> 'SUCCESS' OR $result['result_code'] <> 'SUCCESS' OR $result['trade_state'] <> 'SUCCESS') {
                throw new \Exception($result['err_code_des']);
            }
            # 更新订单状态为已支付
            if($order->total == $result['total_fee'] and $order->status <> Order::Finished) {
                try{
                    DB::beginTransaction();
                    DB::table('orders')->where('id', $id)->update(['status'=>Order::Finished]);
                    DB::table('order_promotions')->where('orderid', $id)->update(['status'=> OrderPromotion::UnReceived]);
                    DB::commit();
                }
                catch (\Exception $exception) {
                    DB::rollback(); echo $exception->getMessage();
                    //Log::info($exception->getMessage());
                }
                $order = Order::find($id);
                $resource = new OrderResource($order);
                return $this->okWithResource($resource);
            }
            else {
//                echo "当前订单状态为".$order->status;
                $order = Order::find($id);
                $resource = new OrderResource($order);
                return $this->okWithResource($resource);
            }
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }

    public function notify(Request $request)
    {   # 支付成功
        $response  = $this->payment->handlePaidNotify(function ($message, $fail) use ($request) {
            if ($message['return_code'] === 'SUCCESS') {
                $request->offsetSet('trade_no', $message['out_trade_no']);
                $order = Order::getOrder($request);
                # 订单已支付
                if(!$order || $order->status == Order::Finished) {
                    return true;
                }
                if (array_get($message, 'result_code') === 'SUCCESS') {
                    $order->paytime = time(); // 更新支付时间为当前时间
                    $order->transaction_id = $message['transaction_id'];
                    $order->status = Order::Finished;
                    event(new PaySuccessEvent($order->id));
                } elseif (array_get($message, 'result_code') === 'FAIL') {
                    $order->status = Order::Cancel;
                    $order->note   = '订单支付失败';
                }
            } else {
                return $fail('通信失败，请稍后再通知我');
            }
            $order->save();
            return true;
        });
        return $response;
    }


}
