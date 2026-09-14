<?php

namespace App\Http\Controllers;

use App\Classes\ImageClass;
use App\Http\Requests\Admin\CategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;
use Exception;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::latest()->get();

        return response()->json([
            'message' => 'Categories retrieved successfully',
            'data' => $categories,
        ]);
    }

    public function store(CategoryRequest $request)
    {
        // $validated = $request->validate([
        //     'name' => 'required|string|max:255|unique:categories,name',
        //     'description' => 'nullable|string',
        //     'image' => 'nullable|string',
        //     'is_active' => 'boolean',
        // ]);
        $create_image = new ImageClass(directory: 'images/categories');
        $category = Category::create([
            'name' => $request->name,
            'description' => $request->description,
            'image' => $create_image->store($request->image),
            'is_active' =>$request->is_active
        ]);

        return response()->json([
            'message' => 'Category created successfully',
            'data' => $category,
        ], 201);
    }

    public function show(Category $category) // $category come from route(categories/2)
                                            // then in this function number 2 will be come $category = Category::findOrFail(2);
                                            // that why we can retrieve a specific category
                                            // in this case the function param's name must match to route param's name, order uneccessary
    {
        return response()->json([
            'message' => 'Category retrieved successfully',
            'data' => $category,
        ]);
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $data = $request->validated(); // this will catch only requested data
        $old_image = $category->getRawOriginal('image');
        $new_image = null;
        $image_class = new ImageClass(directory: 'images/categories');
        try{
            $new_image = $image_class->store($request->image);
            $data['image'] = $new_image;
        }catch(Exception $e){
            $image_class->delete($new_image);
            throw $e;
        }
        $category->update($data);
        $image_class->delete($old_image);
        return response()->json([
            'message' => 'Category updated successfully',
            'data' => $category,
        ]);
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully',
        ]);
    }
}