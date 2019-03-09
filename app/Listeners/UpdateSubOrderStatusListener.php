<?php

namespace App\Listeners;

use App\Models\Customer\OrderPromotion;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateSubOrderStatusListener
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
     * 更新子订单状态 为 已支付
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        # 总订单id
        $id = $event->id;
        DB::table('order_promotions')
            ->where('orderid', $id)
            ->update(['status' => OrderPromotion::UnReceived]);
    }
}
