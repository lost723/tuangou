<?php

namespace App\Http\Controllers\Customer;

use App\Events\RefundSuccessEvent;
use App\Http\Controllers\BasePaymentController;
use App\Http\Controllers\Log\RefundLog;
use App\Models\Customer\OrderPromotion;
use App\Models\Customer\RefundOrder;
use Illuminate\Http\Request;

class RefundController extends BasePaymentController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth', ['except' => ['notify']]);
    }

    # check 活动是否已结束
    public function checkTimeOut($id)
    {
        if (!($order = OrderPromotion::checkOrderPromotionsEnableRefund($id))) {
            throw new \Exception('该商品所参与的活动已结束,请勿退款！');
        }
        return $order;
    }

    /**
     * 某商品活动订单发起退款
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function Refund(Request $request)
    {   # todo 日志数据格式待整理
        try {
            $id = $request->post('id');
            $order = $this->checkTimeOut($id);
//            $result = $this->payment->refund->byTransactionId($order->transaction_id, $order->ordersn,
//                $order->ototal*100, $order->total*100);dump($order);die;
            $result = $this->payment->refund->byOutTradeNumber($order->trade_no, $order->ordersn,
                $order->ototal*100, $order->total*100);
            if ($result['return_code'] <> 'SUCCESS' ||$result['result_code'] <> 'SUCCESS') {
                throw new \Exception($result['err_code_des']);
            }
            # 更新订单状态为退款中
            OrderPromotion::updatePromotionStatus(OrderPromotion::Refunding, $order->id);
            return $this->okWithResource([], '发起退款成功');
        } catch (\Exception $exception) {
            $messsage= $exception->getMessage();
            return $this->warning($exception->getMessage());
        }

    }

    /**
     * 退款通知
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \EasyWeChat\Kernel\Exceptions\Exception
     */
    public function notify(Request $request)
    {
        $response = $this->payment->handleRefundedNotify(function ($message, $reqInfo, $fail) use ($request) {
            if($message['return_code'] <> 'SUCCESS') {
                return $fail($message['return_msg']);
            }
            # 查找订单  更新订单状态
            $request->offsetSet('ordersn', $reqInfo['out_refund_no']);
            $order = OrderPromotion::getOrderPromotion($request);
            # 已退款
            if(!$order || $order->status == OrderPromotion::Refund) {
                return true;
            }
            if($reqInfo['refund_status'] == 'SUCCESS') {
                $order->status = OrderPromotion::Refund;
                $order->refundtime = time();
                event(new RefundSuccessEvent($order->id));
            }
            else if($reqInfo['refund_status'] == 'CHANGE') {
                $order->status = OrderPromotion::CHANGE;
                $order->refundtime = time();
            }
            else if($reqInfo['refund_status'] == 'REFUNDCLOSE') {
                $order->status = OrderPromotion::REFUNDCLOSE;
                $order->refundtime = time();
            }
            $order->save();
            return true;
        });
        return $response;
    }

}
