<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\MenuRequest;
use App\Models\Menu;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with([
            'categories',
            'products',
        ])
            ->latest('date')
            ->get();

        return response()->json([
            'message' => 'Menus retrieved successfully',
            'data' => $menus,
        ]);
    }
    public function store(MenuRequest $request)
    {
        $validated = $request->validated();
        $menu = DB::transaction(
            function () use ($validated) {
                $menu = Menu::create([
                    'date' => $validated['date'],
                ]);

                $menu->categories()->sync( // sync here mean to make relationship match to what we provided
                    $validated['categories']
                );

                /**       
                 *   if sync([1, 2]) mean "Make this menu related to categories 1 and 2."
                 *   so laravel will insert those categories into intermediate table(menu_categories table) with the current menu
                 *   id | menu_id | category_id
                 *   --------------------------
                 *   1  | 1       | 1
                 *   2  | 1       | 2 
                 */

                foreach ($validated['products'] as $product) {
                    $menu->products()->attach( // attach here mean create
                        $product['product_id'],
                        [
                            'price' => $product['price'],
                            'is_available' => $product['is_available'] ?? true,
                        ]
                    );
                    /**
                     * or 
                     * $menu->products()->sync([
                     *       1 => ['price' => 2.50],
                     *       4 => ['price' => 1.50],
                     *       5 => ['price' => 3.00],
                     *   ]);   
                     */
                }
                /**
                 * both sync and attach are very similar because both use to create record-relationship to pivot table,
                 * but attach will be useful if after we created menu and later want to add another product to menu,
                 * it will simply add that menu
                 * but sync will behave differently, if we want to update menu, it will retrieve all record related to menu to compare with the new record we have just added,
                 * if match keep, not remove, similar(match id but not price) will update, and don't have will create
                 */
                return $menu;
            }
        );

        return response()->json([
            'message' => 'Menu created successfully',
            'data' => $menu->load([
                'categories',
                'products',
            ]),
        ], 201);
    }
    public function update(MenuRequest $request, Menu $menu)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $menu) {

            $menu->update([
                'date' => $validated['date'],
            ]);

            $menu->categories()->sync(
                $validated['categories']
            );

            $products = [];

            foreach ($validated['products'] as $product) {
                $products[$product['product_id']] = [
                    'price' => $product['price'],
                    'is_available' => $product['is_available'] ?? true,
                ];
            }

            $menu->products()->sync($products);
        });

        return response()->json([
            'message' => 'Menu updated successfully',
            'data' => $menu->load([
                'categories',
                'products',
            ]),
        ]);
    }
}