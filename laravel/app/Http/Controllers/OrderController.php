<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Product;
use App\Models\PickupSlot;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function store(StoreOrderRequest $request)
    {
        $validated = $request->validated();

        $order = DB::transaction(function () use ($request, $validated) {
            $slot = PickupSlot::query()
                ->whereKey($validated['pickup_slot_id']) // Filters by the model's primary key. mean target to the primary key of table. it like normal where, but it can be queried by only primary key.
                // If the requested slot ID is 3, this selects the slot whose primary key is 3.
                ->lockForUpdate() //transaction is active.
                //Suppose two customers attempt to reserve the last available place at the same time.
                // Both transactions need to check capacity. The lock makes them serialize their access to that slot row: one obtains the lock first, and the other waits.
                ->firstOrFail(); //Retrieves the first matching slot. If no slot exists, Laravel throws a model-not-found exception, normally producing a 404 response.
            $now = now();

            $slotStart = \Carbon\Carbon::parse(
                $slot->date->format('Y-m-d') . ' ' . $slot->start_time
            );

            if (!$slotStart->isAfter($now)) {
                throw ValidationException::withMessages([
                    'pickup_slot_id' => 'This pickup slot is no longer available.',
                ]);
            }
            if (!$slot->is_active) {
                throw ValidationException::withMessages([
                    'pickup_slot_id' => 'This pickup slot is currently unavailable.',
                ]);
            }
            $menu = Menu::query()
                ->whereDate('date', $slot->date)
                ->with(['products.category'])
                ->first();

            if (!$menu) {
                throw ValidationException::withMessages([
                    'pickup_slot_id' => 'No menu is available for this pickup date.',
                ]);
            }
            // $products = Product::query()
            //     ->with('category')
            //     ->whereIn( // use to query with multiple ids
            //         'id',
            //         collect($validated['items'])
            //             ->pluck('product_id')
            //     )// Selects products whose IDs are in the provided list. This is more efficient than running a separate database query for every product.
            //     ->get()
            //     ->keyBy('id'); //Changes the collection's keys to the product IDs.
            // // Remember: keyBy('id') organizes the collection by ID ([5 => Product model for Coca,]),
            // // while get($id) retrieves the value stored under that key.
            // // It doesn't return the key itself.

            $menuProduct = $menu->products->keyBy('id');

            // if ($menuProduct->count() !== count($validated['items'])) {
            //     throw ValidationException::withMessages([
            //         'items' => 'One or more selected products do not exist.',
            //     ]);
            // }

            $total = 0;

            foreach ($validated['items'] as $item) {
                $product = $menuProduct->get($item['product_id']); // this will return the entire product with that id
                                                                    // and we can use get($id) untill that product is produced by keyBy('id')
                                                                    // Without keyBy('id'), the collection normally has numeric indexes starting from 0,
                                                                    // so $products->get(5) would look for collection key 5, not necessarily the product whose database ID is 5
                if (!$product) {
                    throw ValidationException::withMessages([
                        'items' => 'One or more products are not included in this menu.',
                    ]);
                }
                // Replace is_available with your actual availability field.
                if (!$product->is_active || !$product->category?->is_active || !$product->pivot->is_available) {
                    throw ValidationException::withMessages([
                        'items' => 'One or more selected products are unavailable.',
                    ]);
                }

                $total += $product->pivot->price * $item['quantity'];
            }

            $occupiedOrders = Order::query()
                ->where('pickup_slot_id', $slot->id)
                ->whereIn('status', ['PENDING', 'ACCEPTED', 'PREPARING', 'READY'])
                ->count();

            if ($occupiedOrders >= $slot->capacity) {
                throw ValidationException::withMessages([
                    'pickup_slot_id' => 'This pickup slot is full.',
                ]);
            }

            $order = Order::create([
                'user_id' => $request->user()->id,
                'pickup_slot_id' => $slot->id,
                'status' => 'PENDING',
                'total' => $total,
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                $product = $menuProduct->get($item['product_id']);

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->pivot->price,
                ]);
            }

            return $order;
        });

        return response()->json([
            'message' => 'Order created successfully.',
            'order' => $order->load(['items.product', 'pickupSlot']),
        ], 201);
    }
}
