<?php

namespace App\Listeners;

use App\Models\Customer\OrderPromotion;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

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
        try {
            DB::beginTransaction();
            # 查询子订单
            $order = DB::table('order_promotions')->where('id', $id)->first();
            # 更新子订单状态为 已退款
            DB::table('order_promotions')->where('id', $id)->update(['status'=>OrderPromotion::Refund]);
            # 更新商户 和 团长活动销量
            DB::table('leader_promotions')
                ->where('id', $order->lpmid)
                ->decrement('sales', $order->num);
            $promotions = DB::table('promotions')->where('id', $order->promotionid)->first();
            DB::table('promotions')
                ->where('id', $order->promotionid)
                ->decrement('sales', $order->num);
            #  如果有库存 则更新库存
            if($promotions->stockable) {
                DB::table('promotions')->where('id', $promotions->id)->increment('stock',$order->num);
            }
            DB::commit();
        }
        catch (\Exception $exception) {
            DB::rollback();
            Log::info($exception->getMessage());
        }


    }
}
