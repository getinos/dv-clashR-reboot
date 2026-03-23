<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Character extends Model
{
    protected $table = 'characters';

    protected $fillable = [
        'name',
        'description',
        'base_price',
        'hp',
        'damage',
        'speed',
        'range',
        'role',
        'abilities',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'integer',
            'hp' => 'integer',
            'damage' => 'integer',
            'speed' => 'integer',
            'range' => 'integer',
            'abilities' => 'array',
        ];
    }
}
