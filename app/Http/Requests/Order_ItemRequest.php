<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Order_ItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization logic can be added here if needed
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'order_id' => 'required|exists:orders,id', // Ensure the order exists
            'product_id' => 'required|exists:products,id', // Ensure the product exists
            'quantity' => 'required|integer|min:1', // Quantity must be a positive integer
            'price' => 'required|numeric|min:0', // Price must be a non-negative number
            //
        ];
    }
}