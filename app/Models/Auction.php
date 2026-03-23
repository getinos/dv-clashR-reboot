<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auction extends Model
{
    use HasFactory;

    protected $fillable = [
        'character_id',
        'current_price',
        'current_winner_team_id',
        'status',
        'ends_at',
    ];

    public $timestamps = true;

    protected $casts = [
        'current_price' => 'integer',
        'current_winner_team_id' => 'integer',
        'ends_at' => 'datetime',
    ];
}
