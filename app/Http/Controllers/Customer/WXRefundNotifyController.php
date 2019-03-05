<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Weixin\OpenSSLAESController;
use App\Http\Controllers\Weixin\Wxpay\WxPayNotify;
use App\Models\Customer\OrderPromotion;
use App\Models\Customer\RefundOrder;
use Illuminate\Support\Facades\DB;


class WXRefundNotifyController extends WxPayNotify
{
    public function decryptData($data, $key)
    {
        $opensslAes = new OpenSSLAESController($key, 'AES-256-ECB');
        return $opensslAes->decrypt($data);
    }

    /**
     * 重写回调处理函数
     * @param WxPayNotifyResults $data 回调解释出的参数
     * @param WxPayConfigInterface $config
     * @param string $msg 如果回调处理失败，可以将错误信息输出到该方法
     * @return true回调出来完成不需要继续回调，false回调处理未完成需要继续回调
     */
    public function NotifyProcess($objData, $config, &$msg)
    {
        $data = $objData->GetValues();
        if($data['return_code'] != 'SUCCESS' || !array_key_exists('req_info', $data)) {
            return false;
        }
        $req_info = $this->decryptData(base64_decode($data['req_info'], true), md5($config->GetKey()));
        $req_info = $this->FromXml($req_info); # xml to array

        # 退款成功
        if($req_info['refund_status'] == 'SUCCESS') {
            # 更新退款数据  退款表单
            if(($refund = RefundOrder::findOrderByRefundId($req_info['refund_id']))) {
                try{
                    $order = OrderPromotion::findOrderById($refund['order_promotionid']);
                    DB::beginTransaction();
                    OrderPromotion::updatePromotions(OrderPromotion::Refund, $order['id']); # 更新用户订单状态至 已退款
                    RefundOrder::updateRefundStatus(RefundOrder::Finished, $refund['id']); # 更新 退款单状态至 已退款
                    LeaderPromotion::decPromotionSales($order['promotionid'],$order['num']); # 更新 团长活动销量
                    LeaderPromotion::decBusinessPromotionSales($order['promotionid'],$order['num']); # 更新商户活动销量
                    DB::commit();
                    return true;
                }
                catch (\Exception $exception) {
                    DB::rollback();
                }
            }

            return false;
        }
        # 退款 异常
        # 退款 关闭

    }
}
