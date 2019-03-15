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
        'App\Events\LeaderCheckEvent' => [
            'App\Listeners\LeaderCheckListener',
        ],
        'App\Events\LeaderVerifyEvent' => [
            'App\Listeners\LeaderVerifyListener',
        ],
        'App\Events\PaySuccessEvent' => [
            'App\Listeners\PaySuccessListener',
        ],
        'App\Events\RefundSuccessEvent' => [
            'App\Listeners\RefundSuccessListener'
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
