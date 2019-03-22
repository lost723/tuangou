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
     *  关联 创建订单  + 取消订单事件
     *  创建订单 +销量-库存 取消订单 -销量+库存
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {   Log::info("更新销量+库存".$event->id);
        try{
            # 主订单id
            $id = $event->id;
            $order_promotions = DB::table('order_promotions')->where('orderid', $id)->get(['lpmid', 'promotionid', 'num']);
            Log::info(print_r($order_promotions, 1));
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
                Log::info("处理主动取消订单");
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
            DB::rollback();
            Log::info($exception->getMessage());
        }

    }
}
