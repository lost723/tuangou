<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

class UpdateCargosCountListener
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
     * 更新加入购物车次数
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        try{
            $id = $event->id;
            $result = DB::table('leader_promotions')->where('id', $id)->select('id', 'promotionid')->first();
            DB::beginTransaction();
            DB::table('leader_promotions')->where('id', $id)->increment('cargoscount');
            DB::table('promotions')->where('id', $result->promotionid)->increment('cargoscount');
            DB::commit();
        }
        catch (\Exception $exception) {
            DB::rollback();
            Log::info($exception->getMessage());
        }
    }
}
