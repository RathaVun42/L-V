<?php

namespace App\Services;

use App\Models\CanteenSetting;
use App\Models\PickupSlot;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PickupSlotService
{
    public function generate(Carbon $date): Collection
    {
        $setting = CanteenSetting::first();

        if (!$setting) {
            throw new \Exception('Canteen settings not found.');
        }

        // Check whether the canteen is open on this day
        $day = strtolower($date->format('l'));

        if (!$setting->$day) {
            return collect([
                'message' => 'Today, shopping isn\'s availble.'
            ]);
        }

        $start = Carbon::parse(
            $date->toDateString() . ' ' . $setting->pickup_start_time
        );

        $end = Carbon::parse(
            $date->toDateString() . ' ' . $setting->pickup_end_time
        );

        $slots = collect();

        while ($start->lt($end)) {

            $slotStart = $start->copy();

            $slotEnd = $start->copy()
                ->addMinutes($setting->slot_duration);

            // Don't create a slot that goes past closing time
            if ($slotEnd->gt($end)) {
                break;
            }

            $slot = PickupSlot::firstOrCreate(
                [
                    'date' => $date->toDateString(),
                    'start_time' => $slotStart->format('H:i:s'),
                    'end_time' => $slotEnd->format('H:i:s'),
                ], // these three attributes will decide whether create or return,
                   // this will use the three attributes to find a match row in database, 
                   //if match it will return that row, 
                   //not, will create and add others values
                [
                    'capacity' => $setting->slot_capacity,
                ] // value here is additional values use to create new record if record doesn't exist
            );

            $slots->push($slot);

            $start->addMinutes($setting->slot_duration);
        }

        return $slots;
    }
}