<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'pickup_start_time',
    'pickup_end_time',
    'slot_duration',
    'slot_capacity',
    'monday',
    'tuesday',
    'wednesday',
    'thursday',
    'friday',
    'saturday',
    'sunday',
])]
class CanteenSetting extends Model
{

    protected function casts(): array // cast function will casts all model data into a specific type to our database
    {
        return [
            'monday' => 'boolean',
            'tuesday' => 'boolean',
            'wednesday' => 'boolean',
            'thursday' => 'boolean',
            'friday' => 'boolean',
            'saturday' => 'boolean',
            'sunday' => 'boolean',
        ];
    }
}