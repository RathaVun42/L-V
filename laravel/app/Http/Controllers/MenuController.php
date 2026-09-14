<?php

namespace App\Http\Controllers;

use App\Models\Menu;

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
}