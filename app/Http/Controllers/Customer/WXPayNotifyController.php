<?php

namespace App\Http\Controllers\Customer;


use App\Http\Controllers\Weixin\Wxpay\WxPayNotify;
use App\Models\Customer\LeaderPromotion;
use App\Models\Customer\Order;
use App\Models\Customer\OrderPromotion;
use Illuminate\Http\Request;


class WXPayNotifyController extends WxPayNotify
{
    //重写回调处理函数
    /**
     * @param WxPayNotifyResults $data 回调解释出的参数
     * @param WxPayConfigInterface $config
     * @param string $msg 如果回调处理失败，可以将错误信息输出到该方法
     * @return true回调出来完成不需要继续回调，false回调处理未完成需要继续回调
     */
    public function NotifyProcess($objData, $config, &$msg)
    {
       # 参数 和 签名校验
       if(!($this->check($objData, $config))) {
           return false;
       }
       $data = $objData->GetValues();
       # 更新订单数据 更新团长活动销量 更新商户活动销量
       $request = new Request();
       $request->offsetSet('trade_no', $data['out_trade_no']);
       $order = Order::getOrder($request);
       if(!empty($order) && ($order['status'] == Order::Unpaid ) && ($data['total'] / 100 == $order['total'])) {
            $record = [];
            $record['transaction_id'] = $data['transaction_id'];
            $record['status']         = Order::Finished;
            try{
                Order::updateOrder($data,$order['id']); # 更新用户订单状态
                $order_promotions = Order::getSubPromotions($order['id']); # 该订单下的所有子订单
                DB::beginTransaction();
                foreach($order_promotions as $key=>$val) {
                    OrderPromotion::updatePromotionStatus(OrderPromotion::UnReceived, $val['id']); # 订单状态->已支付
                    LeaderPromotion::incPromotionSales($val['promotionid'], $val['num']); # 更新团长订单销量
                    LeaderPromotion::incBusinessPromotionSales($val['promotionid'], $val['num']); # 更新商户活动销量
                }
                DB::commit();
                return true;
            }
            catch (\Exception $exception) {
                DB::rollback();
                return false;
            }
       }
       return false;
    }
}
