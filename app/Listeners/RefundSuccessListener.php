<?php

namespace App\Listeners;

use App\Models\Customer\OrderPromotion;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class RefundSuccessListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $id = $event->id;
        DB::table('order_promotions')
            ->where('orderid', $id)
            ->update(['status' => OrderPromotion::UnReceived]);
        # 查询子订单
        $order = DB::table('order_promotions')->where('id', $id)->first();
        # 更新子订单状态为 已退款
        $order->status = OrderPromotion::Refund;
        $order->save();
        # 更新商户 和 团长活动销量
        DB::table('leader_promotions')
            ->where('id', $order['lpmid'])
            ->decrement('sales', $order['num']);
        DB::table('promotions')
            ->where('id', $order['promotionid'])
            ->decrement('sales', $order['num']);
    }
}
