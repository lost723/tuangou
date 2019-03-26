<?php

namespace App\Listeners;

use App\Common\ProfitShare;
use App\Http\Controllers\Customer\LeaderPromotionController;
use App\Models\Customer\LeaderPromotion;
use App\Models\Customer\Order;
use App\Models\Customer\OrderPromotion;
use EasyWeChat\Factory;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LeaderCheckListener
{
    # 团长签收
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {

        # todo 更新用户订单状态且生成 提货码
        # 1、更新实际签收数量
        # 2、生成核销码 更新消费者订单状态为配送中
        # 3、签收消息通知


        try {
            Log::info("进入 签收入口 签收团长活动id".$event->id);
            $id = $event->id;
            $realcount = $event->count;
            $leaderPromtions = DB::table('leader_promotions')->where('id', $id)->where('active', LeaderPromotion::Active)
                ->select('id', 'promotionid', 'status')->first();
            # 更新商户活动实际签收数量
            DB::table('promotions')->where('id', $leaderPromtions->promotionid)->increment('checkcount', $realcount);

            $orderPromotions = DB::table('order_promotions')
                ->where('lpmid', $id)
                ->where('status', OrderPromotion::UnReceived)
                ->select('id')->get();
            # 生成核销码 更新子订单状态为配送中
            foreach ($orderPromotions as $key=>$val) { $id = 2666;
                DB::table('order_promotions')->where('id', $val->id)->update(
                    [   //sprintf("%02X", $id%256)."-".
                        'checkcode'     =>  sprintf("%06X", (12345678+($val->id)*10)%(10000000)),
                        'status'        =>  OrderPromotion::Dispatched,
                    ]
                );
            }
            # todo 签收消息通知
        }
        catch (\Exception $exception) {
            Log::info($exception->getMessage());
        }
    }
}
