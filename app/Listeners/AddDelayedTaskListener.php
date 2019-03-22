<?php

namespace App\Listeners;

use App\Jobs\CheckOrderTimeout;
use App\Models\Customer\Order;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class AddDelayedTaskListener
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
        try{
            Log::info("进入AddDelayedTaskListener 所处理的订单id:".$event->id);
            $reuslt = CheckOrderTimeout::dispatch($event->id)->delay(now()->addMinutes(Order::TimeOut));
        }
        catch (\Exception $exception) {
            Log::info("异常AddDelayedTaskListener:".$exception->getMessage());
        }

    }
}
