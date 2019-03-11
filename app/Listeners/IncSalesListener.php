<?php

namespace App\Listeners;

use App\Models\Customer\Order;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class IncSalesListener
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
        # 总订单id
        $id = $event->id;
        # 获取支付订单下的所有商品订单
        $suborders = Order::getSubPromotions($id);

        foreach ($suborders as $key => $val) {
            # 更新团长销量 和 商户销量及库存
            DB::table('leader_promotions')
                ->where('id', $val['lpmid'])
                ->increment('sales', $val['num']);
            DB::table('promotions')
                ->where('id', $val['promotionid'])
                ->increment('sales', $val['num']);
        }

    }
}
