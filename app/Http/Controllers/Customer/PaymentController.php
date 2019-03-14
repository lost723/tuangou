<?php

namespace App\Http\Controllers\Customer;

use App\Events\PaySuccessEvent;
use App\Models\Auth\Customer;
use App\Models\Customer\Order;
use function EasyWeChat\Kernel\Support\generate_sign;
use Illuminate\Http\Request;
use App\Http\Controllers\BasePaymentController;
use Illuminate\Support\Facades\DB;


class PaymentController extends BasePaymentController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth', ['except' =>  ['notify']]);
    }

    # 检测订单是否超时
    # 超时更新订单状态
    public function checkTimeOut($id)
    {
        if(!($order = Order::checkOrder($id))) {
//            try{
//                DB::beginTransaction();
//                Order::cancelCasecadeOrder($id); # 更新订单状态
//                DB::commit();
//            }
//            catch (\Exception $exception) {
//                DB::rollback();
//                throw new \Exception($exception->getMessage());
//            }
//            throw new \Exception('订单不存在或已超时！');
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
            # $customer = auth()->user();
            $customer = Customer::find(17);
            $data = [];
            $data['body']               = '团购';
            $data['out_trade_no']       = $order->trade_no;
            $data['total_fee']          = $order->total*100;
            $data['sub_openid']         = $customer->openid;
            $data['trade_type']         = 'JSAPI';
//            $data['profit_sharing']     = 'Y';
            $result = $this->payment->order->unify($data);
            if($result['return_code'] <> 'SUCCESS' ||$result['result_code'] <> 'SUCCESS') {
                throw new \Exception($result['return_msg']);
            }
            $response = [
                'appId'     =>  $this->config['sub_appid'],
                'timeStamp' =>  ''.time(),
                'nonceStr'  =>  $result['nonce_str'],
                'signType'  =>  'MD5',
                'package'   =>  'prepay_id='.$result['prepay_id'],
            ];
            $response['paySign'] = generate_sign($response, $this->config['key'], 'md5');
            return $this->okWithResource($response);
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
                } elseif (array_get($message, 'result_code') === 'FAIL') {
                    $order->status = Order::Cancel;
                    $order->note   = '订单支付失败';
                }
            } else {
                return $fail('通信失败，请稍后再通知我');
            }
            $order->save();
            event(new PaySuccessEvent($order->id));
            return true;
        });
        return $response;
    }
}
