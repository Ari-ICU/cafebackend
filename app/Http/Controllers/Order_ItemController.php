<?php

namespace App\Http\Controllers;

use App\Http\Requests\Order_ItemRequest;
use App\Models\Order_Item;

class Order_ItemController extends Controller
{
    //
    public function index()
    {
        // Logic to retrieve and return all order items
        $orderItems = Order_Item::all();
        if ($orderItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No order items found'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Order items retrieved successfully',
            'data' => $orderItems
        ], 200);
    }
    public function show($id)
    {
        // Logic to retrieve and return a specific order item by ID
        $orderItem = Order_Item::find($id); 
        if (!$orderItem) {
            return response()->json([
                'success' => false,
                'message' => 'Order item not found'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Order item retrieved successfully',
            'data' => $orderItem
        ], 200);
    }
    public function store(Order_ItemRequest $request)
    {
        // Logic to create a new order item
        $orderItem = Order_Item::create($request->validated());
        if (!$orderItem) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order item'
            ], 500);
        }
        return response()->json([
            'success' => true,
            'message' => 'Order item created successfully',
            'data' => $orderItem
        ], 201);
    }    
    public function update(Order_ItemRequest $request, $id)
    {
        // Logic to update an existing order item by ID
        $orderItem = Order_Item::find($id);
        if (!$orderItem) {
            return response()->json([
                'success' => false,
                'message' => 'Order item not found'
            ], 404);
        }
        $orderItem->update($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Order item updated successfully',
            'data' => $orderItem
        ], 200);
    }
    public function destroy($id)
    {
        // Logic to delete an order item by ID
        $orderItem = Order_Item::find($id);
        if (!$orderItem) {
            return response()->json([
                'success' => false,
                'message' => 'Order item not found'
            ], 404);
        }
        
       
        $orderItem->delete();
        return response()->json([
            'success' => true,
            'message' => 'Order item deleted successfully'
        ], 200);
    }
    public function getOrderItemsByOrderId($orderId)
    {
        // Logic to retrieve order items by order ID
        $orderItems = Order_Item::where('order_id', $orderId)->get();
        if ($orderItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No order items found for this order'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Order items retrieved successfully',
            'data' => $orderItems
        ], 200);
    }
}