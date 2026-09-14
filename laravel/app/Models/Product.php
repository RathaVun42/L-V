<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'description',
        'image',
        'is_active',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    public function menus()
    {
        return $this->belongsToMany(
            Menu::class,
            'menu_products' // this is the name of middle table (pivot table) if it has many-many relationship
        )->withPivot([ //When you retrieve the products through this relationship, also retrieve price and is_available from the pivot table.
                    'price',
                    'is_available',
                ]);
        // withPivot() tells Laravel which extra columns from the intermediate menu_products table you want to access through $product->pivot.
    }
}