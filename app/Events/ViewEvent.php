<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

/**
 * 浏览页面事件
 * Class ViewEvent
 * @package App\Events
 */
class ViewEvent extends Event
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $id;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->action = 'view';
        $this->id = $id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
