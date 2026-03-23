<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CharacterDeployed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $characterId;
    public string $characterName;
    public int $gridX;
    public int $gridY;
    public int $teamId;
    public string $teamName;
    public ?string $image;
    public string $role;
    public int $hp;
    public int $damage;
    public int $speed;
    public int $range;
    public int $cooldown;

    public function __construct(
        int $characterId,
        string $characterName,
        int $gridX,
        int $gridY,
        int $teamId,
        string $teamName,
        ?string $image = null,
        string $role = 'Unknown',
        int $hp = 0,
        int $damage = 0,
        int $speed = 0,
        int $range = 0,
        int $cooldown = 0
    ) {
        $this->characterId = $characterId;
        $this->characterName = $characterName;
        $this->gridX = $gridX;
        $this->gridY = $gridY;
        $this->teamId = $teamId;
        $this->teamName = $teamName;
        $this->image = $image;
        $this->role = $role;
        $this->hp = $hp;
        $this->damage = $damage;
        $this->speed = $speed;
        $this->range = $range;
        $this->cooldown = $cooldown;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('battleground');
    }

    public function broadcastAs(): string
    {
        return 'character.deployed';
    }

    public function broadcastWith(): array
    {
        return [
            'character_id' => $this->characterId,
            'character_name' => $this->characterName,
            'grid_x' => $this->gridX,
            'grid_y' => $this->gridY,
            'team_id' => $this->teamId,
            'team_name' => $this->teamName,
            'image' => $this->image,
            'role' => $this->role,
            'hp' => $this->hp,
            'damage' => $this->damage,
            'speed' => $this->speed,
            'range' => $this->range,
            'cooldown' => $this->cooldown,
        ];
    }
}
