<?php

namespace App\Listeners;

use App\Common\ProfitShare;
use App\Http\Controllers\Customer\LeaderPromotionController;
use App\Models\Customer\LeaderPromotion;
use App\Models\Customer\OrderPromotion;
use EasyWeChat\Factory;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class LeaderCheckListener
{
    # 团长签收
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
        $id = $event->id;
        # todo 更新用户订单状态且生成 提货码
        echo "执行团长签收";
    }
}
