<?php

namespace App\Listeners;

use App\Models\Customer\LeaderPromotion;
use App\Models\Customer\Order;
use App\Models\Customer\OrderPromotion;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateSalesAndStockListener
{
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
        try{
            # 主订单id
            $id = $event->id;
            $order_promotions = DB::table('order_promotions')->where('orderid', $id)->where('status', OrderPromotion::Unpaid)->get(['lpmid', 'promotionid', 'num']);
            DB::beginTransaction();
            if($event->sale == 1) {
                foreach ($order_promotions as $key=>$val) {
                    DB::table('leader_promotions')->where('id', $val->lpmid)->increment('sales', $val->num);
                    $promotions = DB::table('promotions')->where('id', $val->promotionid)->first();
                    DB::table('promotions')->where('id', $val->promotionid)->increment('sales', $val->num);
                    if($promotions->stockable) {
                        DB::table('promotions')->where('id', $val->promotionid)->decrement('stock', $val->num);
                    }
                }
            }
            else {
                foreach ($order_promotions as $key=>$val) {
                    DB::table('leader_promotions')->where('id', $val->lpmid)->decrement('sales', $val->num);
                    $promotions = DB::table('promotions')->where('id', $val->promotionid)->first();
                    DB::table('promotions')->where('id', $val->promotionid)->decrement('sales', $val->num);
                    if($promotions->stockable) {
                        DB::table('promotions')->where('id', $val->promotionid)->increment('stock', $val->num);
                    }
                }
            }
            DB::commit();
        }
        catch (\Exception $exception) {
            DB::rollbakc();
            Log::info($exception->getMessage());
        }

    }
}
