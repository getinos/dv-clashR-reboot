<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class BattlegroundDeployment extends Model
{
    protected $table = 'battleground_deployments';

    protected $fillable = [
        'character_id',
        'team_id',
        'grid_x',
        'grid_y',
        'current_hp',
        'last_attack_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'character_id' => 'integer',
            'team_id' => 'integer',
            'grid_x' => 'integer',
            'grid_y' => 'integer',
            'cell_number' => 'integer',
            'current_hp' => 'integer',
            'last_attack_at' => 'datetime',
            'status' => 'string',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $deployment): void {
            // Initialize current HP from character's base HP on first creation
            if (!$deployment->current_hp && $deployment->character) {
                $deployment->current_hp = $deployment->character->hp ?? 100;
            }
            if (!$deployment->current_hp) {
                $deployment->current_hp = 100;
            }
            if (!$deployment->status) {
                $deployment->status = 'alive';
            }
        });

        static::saving(function (self $deployment): void {
            Log::info('Saving deployment', [
                'character_id' => $deployment->character_id,
                'team_id' => $deployment->team_id,
                'grid_x' => $deployment->grid_x,
                'grid_y' => $deployment->grid_y,
                'current_hp' => $deployment->current_hp,
                'status' => $deployment->status,
            ]);

            $deployment->grid_x = self::clampCoordinate((int) $deployment->grid_x);
            $deployment->grid_y = self::clampCoordinate((int) $deployment->grid_y);

            // 10x10 grid numbering is 1-based.
            $deployment->cell_number = ($deployment->grid_y * 10) + $deployment->grid_x + 1;
        });
    }

    protected static function clampCoordinate(int $value): int
    {
        return max(0, min(9, $value));
    }

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }
}
