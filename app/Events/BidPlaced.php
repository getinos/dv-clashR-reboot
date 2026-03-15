<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BidPlaced implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $characterId;
    public int $newPrice;
    public ?int $teamId;
    public ?string $teamName;

    public function __construct(int $characterId, int $newPrice, ?int $teamId = null, ?string $teamName = null)
    {
        $this->characterId = $characterId;
        $this->newPrice    = $newPrice;
        $this->teamId      = $teamId;
        $this->teamName    = $teamName;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('auction');
    }

    public function broadcastAs(): string
    {
        return 'bid.placed';
    }

    public function broadcastWith(): array
    {
        $payload = [
            'character_id' => $this->characterId,
            'new_price'    => $this->newPrice,
        ];
        if ($this->teamId !== null) {
            $payload['team_id'] = $this->teamId;
        }
        if ($this->teamName !== null) {
            $payload['team_name'] = $this->teamName;
        }
        return $payload;
    }
}
