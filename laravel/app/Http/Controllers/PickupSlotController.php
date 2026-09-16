<?php

namespace App\Http\Controllers;

use App\Services\PickupSlotService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PickupSlotController extends Controller
{
    public function index(
        Request $request,
        PickupSlotService $service
    ) {
        $validated = $request->validate([
            'date' => [
                'required',
                'date',
            ],
        ]);

        $date = Carbon::parse(
            $validated['date']
        );

        $slots = $service->generate($date);

        return response()->json([
            'message' => 'Pickup slots retrieved successfully',
            'data' => $slots,
        ]);
    }
}