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
        'App\Events\LeaderCheckEvent' => [               # 团长签收事件
            'App\Listeners\LeaderCheckListener',
        ],
        'App\Events\LeaderVerifyEvent' => [              # 团长核销事件
            'App\Listeners\LeaderVerifyListener',
        ],
        'App\Events\CargosEvent' => [                    # 加入购物车事件
            'App\Listeners\UpdateCargosCountListener',
        ],
        'App\Events\ShareEvent' => [                     # 分享事件
            'App\Listeners\UpdateShareCountListener',    # 更新分享数量
        ],
        'App\Events\ViewEvent' => [                     # 浏览事件
            'App\Listeners\UpdateViewCountListener',
        ],
        'App\Events\CancelOrderEvent' => [              # 取消订单
            'App\Listeners\UpdateSalesAndStockListener',# 更新库存+销量
        ],
        'App\Events\RefundSuccessEvent' => [            # 更新退款数量
            'App\Listeners\RefundSuccessListener'       # 退款成功返还库存 + 销量

        ],
        'App\Events\PaySuccessEvent' => [               #  更新支付成功数量
            'App\Listeners\NoticeListener'              #  消息通知
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
