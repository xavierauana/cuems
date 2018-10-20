<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SystemEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    /**
     * @var int
     */
    public $event;
    /**
     * @var \Illuminate\Database\Eloquent\Model|null
     */
    public $model;

    /**
     * Create a new event instance.
     *
     * @param int                                      $event
     * @param \Illuminate\Database\Eloquent\Model|null $model
     */
    public function __construct(int $event, Model $model = null) {
        //
        $this->event = $event;
        $this->model = $model;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn() {
        return new PrivateChannel('channel-name');
    }
}
