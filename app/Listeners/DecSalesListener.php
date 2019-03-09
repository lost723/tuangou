<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class DecSalesListener
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
     * 退款修改 商户活动销量库存 + 团长活动销量
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $id = $event->id;
        # 查询子订单
        $order = DB::table('order_promotions')->where('id', $id)->first();
        DB::table('leader_promotions')
            ->where('id', $order['lpmid'])
            ->decrement('sales', $order['num']);
        DB::table('promotions')
            ->where('id', $order['promotionid'])
            ->decrement('sales', $order['num']);
    }
}
