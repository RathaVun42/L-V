<?php

namespace App\Http\Controllers;


use App\Http\Requests\Admin\CanteenSettingRequest;
use App\Models\CanteenSetting;

class CanteenSettingController extends Controller
{
    public function show()
    {
        $setting = CanteenSetting::first();

        return response()->json([
            'message' => 'Canteen settings retrieved successfully',
            'data' => $setting,
        ]);
    }

    public function update(CanteenSettingRequest $request)
    {
        $setting = CanteenSetting::first();

        if (!$setting) {
            $setting = CanteenSetting::create(
                $request->validated()
            );
        } else {
            $setting->update(
                $request->validated()
            );
        }

        return response()->json([
            'message' => 'Canteen settings updated successfully',
            'data' => $setting,
        ]);
    }
}