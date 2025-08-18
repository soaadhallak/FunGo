<?php

namespace App\Events;

use App\Models\Place;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlaceCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $place;
    public $title;
    public $body;
    public $data;

    /**
     * Create a new event instance.
     */
    public function __construct(Place $place,string $title,string $body,array $data)
    {
        $this->place=$place;
        $this->title=$title;
        $this->body=$body;
        $this->data=$data;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
