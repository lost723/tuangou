<?php

namespace App\Listeners;

use App\Models\Customer\OrderPromotion;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaySuccessListener
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
     *  todo 更新  支付成功数量
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        try{
            # 总订单id
            $id = $event->id;
            DB::table('order_promotions')
                ->where('orderid', $id)
                ->update(['status' => OrderPromotion::UnReceived]);
            DB::beginTransaction();
            # 获取支付订单下的所有商品订单
            $suborders = DB::table('order_promotions')->where('orderid', $id)->select('lpmid', 'promotionid')->get();
            foreach ($suborders as $key => $val) {
                DB::table('leader_promoions')->where('id', $val->lpmid)->increment('paycount');
                DB::table('promotions')->where('id', $val->promotionid)->increment('paycount');
            }
            DB::commit();
        }
        catch (\Exception $exception) {
            Log::info($exception->getMessage());
        }
    }
}
