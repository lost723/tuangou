<?php

namespace App\Listeners;

use App\Common\ProfitShare;
use App\Http\Controllers\Customer\LeaderPromotionController;
use App\Models\Customer\LeaderPromotion;
use App\Models\Customer\OrderPromotion;
use EasyWeChat\Factory;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProfitShareListener
{
    public $payment;
    public $sharing;
    /**
     * 引用分账句柄
     * @return void
     */
    public function __construct()
    {
        $config = config('wechat.payment.default');
        $this->payment = Factory::payment($config);
        $this->sharing = $this->payment->profit_sharing;
    }

    /**
     * 核销 某商品订单 执行分账
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        # todo 分账异常通知
        $id = $event->id;
        $order = OrderPromotion::getOrderPromotionDetail($id);
        if(!$order || $order->status <> OrderPromotion::Finished) {
            return false;
        }
        $receivers = [];
        $receiver = [];
        # 分账方 团长 分销商 平台
        # $receiver['type'] = 'MERCHANT_ID';
        # $receiver['account'] = 'xxxx';
        #
        # 添加分账用户
        $this->sharing->addReceiver($receiver);
        $this->sharing->addReceiver($receiver);
        $this->sharing->addReceiver($receiver);

        # 创建分账记录
        # create
        # 构建receivers 添加 amount 分账金额字段
        $out_trade_no = ProfitShare::SharePrefix.LeaderPromotionController::createOrderSn();
        # 执行分账操作
        $result = $this->sharing->multiShare($order->transaction_id, $out_trade_no, $receivers);
    }
}
