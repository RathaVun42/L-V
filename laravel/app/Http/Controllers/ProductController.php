<?php

namespace App\Http\Controllers;

use App\Classes\ImageClass;
use App\Http\Requests\Admin\ProductRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Product;
use Exception;

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
        $image_class = new ImageClass(directory:'images/products');
        $image = null;
        if($request->hasFile('image')){
            try{
                $image = $image_class->store($request->image);
            }catch(Exception $e){
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
        $image_class = new ImageClass(directory:'images/products');
        $data = $request->validated();
        $old_image = $product->getRawOriginal('image');
        $new_image = null;
        try{
            $new_image = $image_class->store($request->image);
        }catch(Exception $e){
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
        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully',
        ]);
    }
}