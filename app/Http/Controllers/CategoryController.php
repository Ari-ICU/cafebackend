<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Categories;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    //
    public function index()
    {
        // Logic to retrieve and return all categories
        $categories = Categories::all();
        if ($categories->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No categories found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Categories retrieved successfully',
            'data' => $categories
        ], 200);
    }
    public function show($id)
    {
        // Logic to retrieve and return a specific category by ID
        $category = Categories::find($id);
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Category retrieved successfully',
            'data' => $category
        ], 200);
    }
    public function store(CategoryRequest $request)
    {
        // Logic to create a new category
        $category = Categories::create($request->validated());
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create category'
            ], 500);
        }
        return response()->json([
            'success' => true,
            'message' => 'Category created successfully',
            'data' => $category
        ], 201);
    }
    public function update(CategoryRequest $request, $id)
    {
        // Logic to update an existing category by ID
        $category = Categories::find($id);
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }
        $category->update($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully',
            'data' => $category
        ], 200);
    }
    public function destroy($id)
    {
        // Logic to delete a category by ID
        $category = Categories::find($id);  
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }
        $category->delete();
        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully'
        ], 200);
    }
}