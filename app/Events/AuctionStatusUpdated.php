<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AuctionStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public bool $active;
    public bool $bidActive;

    /**
     * Create a new event instance.
     */
    public function __construct(bool $active, bool $bidActive)
    {
        $this->active = $active;
        $this->bidActive = $bidActive;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): Channel
    {
        return new Channel('auction');
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'status.updated';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'active' => $this->active,
            'bid_active' => $this->bidActive,
        ];
    }
}
