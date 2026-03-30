<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id', 
        'total_amount', 
        'type', 
        'status', 
        'shipping_address', 
        'city_id', 
        'courier', 
        'shipping_cost', 
        'custom_notes', 
        'custom_image', 
        'payment_method', 
        'payment_proof'
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
