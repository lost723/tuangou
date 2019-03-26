<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateViewCountListener
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
     *  更新用户浏览商品 数量
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        try{
            Log::info("ViewListener");
            # 团长挑选活动id
            $id = $event->id;
            DB::beginTransaction();
            $result = DB::table('leader_promotions')->where('id', $id)->select('id', 'promotionid')->first();
            DB::table('leader_promotions')->where('id', $id)->increment('viewcount', 1);
            DB::table('promotions')->where('id', $result->promotionid)->increment('viewcount', 1);
            DB::commit();
        }
        catch (\Exception $exception) {
            DB::rollback();
            Log::info($exception->getMessage());
        }
    }
}
