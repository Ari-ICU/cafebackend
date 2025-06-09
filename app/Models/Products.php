<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    //
    protected $table = 'products';
    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'category_id',
        'image'
    ];
    
    public function category()
    {
        return $this->belongsTo(Categories::class, 'category_id');
    }
    public function orders()
    {
        return $this->belongsToMany(Order_Item::class, 'order_product', 'product_id', 'order_id');
    }
    
}