<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrentBattle extends Model
{
    use HasFactory;

    protected $table = 'current_battle';

    protected $fillable = [
        'team_a_id',
        'team_b_id',
        'status',
    ];

    public $timestamps = true;
}
