<?php

namespace App\Http\Controllers;

use App\Classes\ImageClass;
use App\Http\Requests\Admin\ProductRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Product;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Products retrieved successfully',
            'data' => $products,
        ]);
    }

    public function store(ProductRequest $request)
    {
        $data = $request->validated();
        $image_class = new ImageClass(directory: 'images/products');
        $image = null;
        if ($request->hasFile('image')) {
            try {
                $image = $image_class->store($request->image);
            } catch (Exception $e) {
                $image_class->delete(
                    $image
                );
                throw $e;
            }
        }
        $data['image'] = $image;
        $product = Product::create(
            $data
        );

        return response()->json([
            'message' => 'Product created successfully',
            'data' => $product->load('category'),
        ], 201);
    }

    public function show(Product $product)
    {
        return response()->json([
            'message' => 'Product retrieved successfully',
            'data' => $product->load('category'),
        ]);
    }

    public function update(UpdateCategoryRequest $request, Product $product)
    {
        $image_class = new ImageClass(directory: 'images/products');
        $data = $request->validated();
        $old_image = $product->getRawOriginal('image');
        $new_image = null;
        try {
            $new_image = $image_class->store($request->image);
        } catch (Exception $e) {
            $image_class->delete($new_image);
            throw $e;
        }
        $image_class->delete($old_image);
        $data['image'] = $new_image;
        $product->update(
            $data
        );

        return response()->json([
            'message' => 'Product updated successfully',
            'data' => $product->load('category'),
        ]);
    }


    public function destroy(Product $product)
    {
        try {
            $image = $product->getRawOriginal('image');
            $product->delete();

            $imageClass = new ImageClass(directory: 'images/products');
            $imageClass->delete($image);
            return response()->json([
                'message' => 'Product deleted successfully',
            ]);
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                return response()->json([
                    'message' => 'This product cannot be deleted because it is being used in a menu.',
                ], 409);
            }

            throw $e;
        }
    }
    public function byCategories(Request $request)
    {
        $validated = $request->validate([
            'categories' => [
                'required',
                'array',
                'min:1',
            ],

            'categories.*' => [
                'integer',
                'exists:categories,id',
            ],
        ]);

        $products = Product::whereIn(
            'category_id',
            $validated['categories']
        )
            ->where('is_active', true)
            ->with('category')
            ->get();

        return response()->json([
            'message' => 'Products retrieved successfully',
            'data' => $products,
        ]);
    }
}