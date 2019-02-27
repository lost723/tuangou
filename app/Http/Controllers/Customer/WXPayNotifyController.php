<?php

namespace App\Http\Controllers\Customer;

use App\Customer\Models\Order;
use App\Http\Controllers\Common\Wxpay\WxPayNotify;
use App\Models\Customer\LeaderPromotion;


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
       # TODO
       # 更新订单数据 更新团长活动销量 更新商户活动销量
       $order = Order::findOrderByTradeNo($data['out_trade_no']);
       if(!empty($order) && ($order['status'] == Order::Unpaid ) && ($data['total'] / 100 == $order['total'])) {
            $record = [];
            $record['transaction_id'] = $data['transaction_id'];
            $record['status']         = Order::Finished;
            try{

                $result = Order::updateOrder($data,$order['id']);
                $order_promotions = Order::getSubPromotions($order['id']); # 该订单下的所有子订单
                DB::beginTransaction();
                foreach($order_promotions as $key=>$val) {
                    LeaderPromotion::incPromotionSales($val['promotionid'], $val['num']);
                    LeaderPromotion::incBusinessPromotionSales($val['promotionid'], $val['num']);
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
