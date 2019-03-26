<?php

namespace App\Listeners;

use App\Models\Customer\LeaderPromotion;
use App\Models\Customer\OrderPromotion;
use EasyWeChat\Factory;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LeaderVerifyListener
{
    # 团长核销
    public $payment;
    public $sharing;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $config = config('wechat.payment.default');
        $this->payment = Factory::payment($config);
        $this->sharing = $this->payment->profit_sharing;
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        try{
            # todo 分账异常通知
            $id = $event->id;
            $order = DB::table('order_promotions')->where('id', $id)->select('id', 'lpmid', 'promotionid', 'num', 'status')->first();
            if(!$order || $order->status <> OrderPromotion::Finished) {
                return false;
            }
            # 1、更新核销数量
            DB::table('leader_promotions')->where('id', $order->lpmid)->increment('verifycount', $order->num);
            $count = DB::table('order_promotions')
                ->where('lpmid', $order->lpmid)
                ->where('status', OrderPromotion::Dispatched)
                ->count();
            # 2、 检测未核销订单数量 更新团长活动状态为已完成
            if($count === 0) {
                DB::table('leader_promotions')->where('id', $order->lpmid)->update(['status'=>LeaderPromotion::Finished]);
            }
            # 3、 todo 触发分账+下发核销通知

//            $receivers = [];
//            $receiver = [];
//            # 分账方 团长 分销商 平台
//            # $receiver['type'] = 'MERCHANT_ID';
//            # $receiver['account'] = 'xxxx';
//            #
//            # 添加分账用户
//            $this->sharing->addReceiver($receiver);
//            $this->sharing->addReceiver($receiver);
//            $this->sharing->addReceiver($receiver);
//
//            # 创建分账记录
//            # create
//            # 构建receivers 添加 amount 分账金额字段
//            $out_trade_no = ProfitShare::SharePrefix.LeaderPromotionController::createOrderSn();
//            # 执行分账操作
//            $result = $this->sharing->multiShare($order->transaction_id, $out_trade_no, $receivers);
        }
        catch (\Exception $exception) { echo $exception->getMessage();die;
            Log::info($exception->getMessage());
        }
    }
}
