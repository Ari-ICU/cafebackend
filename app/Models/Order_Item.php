<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order_Item extends Model
{
    //
    protected $table = 'order_items';
    protected $fillable = ['order_id', 'product_id', 'quantity', 'price'];
    protected $primaryKey = 'id';
    public $timestamps = true;
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }
    public function getTotalPriceAttribute()
    {
        return $this->quantity * $this->price;
    }
}