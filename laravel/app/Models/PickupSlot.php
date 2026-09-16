<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'date',
    'start_time',
    'end_time',
    'capacity',
])]
class PickupSlot extends Model
{

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'capacity' => 'integer',
        ];
    }
}