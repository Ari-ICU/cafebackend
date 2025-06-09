<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\Order;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    //
    public function index()
    {
        // Logic to retrieve and return all orders
        $orders = Order::all();
        if ($orders->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No orders found'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Orders retrieved successfully',
            'data' => $orders
        ], 200);
    }
    public function show($id)
    {
        // Logic to retrieve and return a specific order by ID
        $order = Order::find($id);
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Order retrieved successfully',
            'data' => $order
        ], 200);
    }
    public function store(OrderRequest $request)
    {
        // Logic to create a new order
        $order = Order::create($request->validated());
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order'
            ], 500);
        }
        return response()->json([
            'success' => true,
            'message' => 'Order created successfully',
            'data' => $order
        ], 201);
    }
    public function update(OrderRequest $request, $id)
    {
        // Logic to update an existing order by ID
        $order = Order::find($id);
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }
        $order->update($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Order updated successfully',
            'data' => $order
        ], 200);
    }
    public function destroy($id)
    {
        // Logic to delete an order by ID
        $order = Order::find($id);
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }
        $order->delete();
        return response()->json([
            'success' => true,
            'message' => 'Order deleted successfully'
        ], 200);
    }
    public function getOrdersByCustomerId($customerId)
    {
        // Logic to retrieve orders by customer ID
        $orders = Order::where('user_id', $customerId)->get();
        if ($orders->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No orders found for this customer'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Orders retrieved successfully',
            'data' => $orders
        ], 200);
    }
}