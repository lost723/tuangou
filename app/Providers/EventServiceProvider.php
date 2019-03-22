<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\CreateOrderEvent' => [
            'App\Listeners\UpdateSalesAndStockListener', # 更新库存+销量
            'App\Listeners\AddDelayedTaskListener',      # 添加延迟任务检测订单超时
        ],
        'App\Events\LeaderCheckEvent' => [
            'App\Listeners\LeaderCheckListener',
        ],
        'App\Events\LeaderVerifyEvent' => [
            'App\Listeners\LeaderVerifyListener',
        ],
        'App\Events\CancelOrderEvent' => [              # 取消订单
            'App\Listeners\UpdateSalesAndStockListener',# 更新库存+销量
        ],
        'App\Events\RefundSuccessEvent' => [
            'App\Listeners\UpdateSalesAndStockListener',# 更新库存+销量
//            'App\Listeners\RefundSuccessListener'
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
