<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //
    protected $table = 'orders';
    protected $fillable = ['user_id', 'status', 'total_amount', 'created_at'];
    protected $primaryKey = 'id';
    public $timestamps = true;
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function orderItems()
    {
        return $this->hasMany(Order_Item::class, 'order_id');
    }
    public function getTotalAmountAttribute()
    {
        return $this->orderItems->sum(function ($item) {
            return $item->quantity * $item->price;
        });
    }
    public function getStatusAttribute($value)
    {
        $statuses = [
            0 => 'Pending',
            1 => 'Processing',
            2 => 'Completed',
            3 => 'Cancelled',
        ];
        return $statuses[$value] ?? 'Unknown';
    }
}