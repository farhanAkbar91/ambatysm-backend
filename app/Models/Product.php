<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
        'price',
        'stock',
        'image'
    ];

    public function reviews()
    {
        return $this->hasManyThrough(
            Review::class,
            OrderItem::class,
            'product_id', // Foreign key on order_items table...
            'order_id',   // Foreign key on reviews table...
            'id',         // Local key on products table...
            'order_id'    // Local key on order_items table...
        );
    }

    public function stocks()
    {
        return $this->hasMany(ProductStock::class);
    }
}
