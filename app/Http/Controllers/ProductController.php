<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Exception;

class ProductController extends Controller
{
    public function index()
    {
        try {
            $products = Products::all();

            if ($products->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No products found'
                ], 404);
            }

            $products->transform(function ($product) {
                $product->image_url = $product->image ? asset('storage/' . $product->image) : null;
                return $product;
            });

            return response()->json([
                'success' => true,
                'message' => 'Products retrieved successfully',
                'data' => $products
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving products',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $product = Products::find($id);

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            if (!$product->is_available) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product is not available'
                ], 404);
            }

            $product->image_url = $product->image ? asset('storage/' . $product->image) : null;

            return response()->json([
                'success' => true,
                'message' => 'Product retrieved successfully',
                'data' => $product
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving product',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(ProductRequest $request)
    {
        try {
            $validated = $request->validated();
            $imagePath = null;

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('uploads', 'public');
            }

            $product = Products::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'price' => $validated['price'],
                'stock' => $validated['stock'],
                'category_id' => $validated['category_id'],
                'is_available' => $validated['is_available'],
                'image' => $imagePath,
            ]);

            if (!$product) {
                if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create product'
                ], 500);
            }

            $product->image_url = $product->image ? asset('storage/' . $product->image) : null;

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully',
                'data' => $product
            ], 201);

        } catch (Exception $e) {
            if (isset($imagePath) && $imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            return response()->json([
                'success' => false,
                'message' => 'Error creating product',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

  public function update(ProductRequest $request, $id)
{
    try {
        $product = Products::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        $validated = $request->validated();
        $imagePath = $product->image;

        // ✅ Delete existing image if image_deleted is true
        if ($request->boolean('image_deleted')) {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = null;
        }

        // ✅ Upload new image if present
        if ($request->hasFile('image')) {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('uploads', 'public');
        }

        // ✅ Update product
        $updated = $product->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'category_id' => $validated['category_id'],
            'is_available' => $validated['is_available'],
            'image' => $imagePath,
        ]);

        if (!$updated) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update product'
            ], 500);
        }

        $product = $product->fresh();
        $product->image_url = $product->image ? asset('storage/' . $product->image) : null;

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => $product
        ]);

    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error updating product',
            'error' => $e->getMessage(),
        ], 500);
    }
}


    public function destroy($id)
    {
        try {
            $product = Products::find($id);

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully'
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting product',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getProductsByCategoryId($categoryId)
    {
        try {
            $products = Products::where('category_id', $categoryId)->get();

            if ($products->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No products found for this category'
                ], 404);
            }

            $products->transform(function ($product) {
                $product->image_url = $product->image ? asset('storage/' . $product->image) : null;
                return $product;
            });

            return response()->json([
                'success' => true,
                'message' => 'Products retrieved successfully',
                'data' => $products
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving products by category',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}