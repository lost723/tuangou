<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Customer\RefundOrder;
use App\Http\Controllers\Weixin\Wxpay\WxPayApi;
use App\Http\Controllers\Weixin\Wxpay\WxPayRefund;
use App\Http\Controllers\Weixin\Wxpay\WxPayUnifiedOrder;
use App\Http\Controllers\Weixin\WXPayConfigController;
use App\Http\Controllers\Weixin\WXPayController;

class RefundController extends Controller
{

    /**
     * 订单退款
     * @param $id   id为用户所购买的某个商品的订单id
     * @return \Illuminate\Http\JsonResponse
     */
    public function refundOrder($id)
    {
        try{
            # 查询订单 (已支付 且 活动未结束)
            if(!($order = OrderPromotion::checkOrderPromotions($id))) {
                throw new \Exception('该商品所参与的活动已结束,请勿退款！');
            }
            # doRefund 创建退款单
            if($refundArgs = $this->createRefund($order)) {
                throw new \Exception('退款订单创建失败');
            }
            # 发起退款请求
            if($data = $this->doRefund($refundArgs)) {
                # 更新退款表状态 订单状态
                # check result_code refund_fee #update refund_id status
                if($data['result'] != 'SUCCESS') {
                    throw new \Exception($data['err_code'].':'.$data['err_code_des']);
                }
                $refundOrder = RefundOrder::findOrderByRefundNo($data['out_refund_no']);
                if(($refundOrder['status'] == RefundOrder::Refunding) && ($refundOrder['refund'] == ($data['refund_fee'] / 100))) {
                    # update 退款表 订单状态表
                    $updateArr = [];
                    $updateArr['refund_id'] = $data['refund_id'];
                    $updateArr['status'] = RefundOrder::Refunding;
                    try{
                        DB::beginTransaction();
                        # 更新退款单状态
                        RefundOrder::updateRefund($updateArr, $refundOrder['id']);
                        # 更新用户订单状态为已退款
                        DB::table('order_promotions')
                            ->where('id',$refundOrder['order_promotionid'])
                            ->update(['status' => OrderPromotion::Refunding]);
                        OrderPromotion::findOrderById();
                        DB::commit();
                    }
                    catch (\Exception $exception){
                        DB::rollback();
                        return $this->warning($exception->getMessage());
                    }
                }
                else {
                    # 订单异常
                }
            }
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }




    # 创建退款订单
    public function createRefund($data)
    {
        $customer = auth()->user();
        $record = [];
        $record['customerid']            = $customer->id;
        $record['orderid']               = $data['orderid'];
        $record['order_promotionid']     = $data['order_promotionid'];
        $record['trade_no']              = $data['trade_no'];
        $record['transaction_id']        = $data['transaction_id'];
        $record['total']                 = $data['total'];
        $record['refund']                = $data['refund'];
        $record['refund_no']             = RefundOrder::RefundPrefix.LeaderPromotionController::createOrderSn();
        $record['status']                = RefundOrder::Refunding;
        $record['note']                  = $data['note'];
        # 生成退款订单
        if(RefundOrder::createRefund($record, $record['order_promotionid'])) {
            return $record;
        }
        else {
            return false;
        }
    }

    # doRefund 发起退款请求
    public function doRefund($data)
    {
        $input = new WxPayRefund();
        $input->SetTransaction_id($data['transaction_id']);
        $input->SetTotal_fee($data['total']*100);
        $input->SetRefund_fee($data['refund']*100);

        $config = new WXPayConfigController();
        $input->SetOut_refund_no($data['refund_no']);
        $input->SetOp_user_id($config->GetMerchantId());
        return WxPayApi::refund($config, $input);
    }
}
