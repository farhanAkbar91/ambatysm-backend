<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductStock extends Model
{
    protected $fillable = [
        'product_id',
        'size',
        'color',
        'stock',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Booted method to handle model events.
     */
    protected static function booted()
    {
        static::saved(function ($productStock) {
            $productStock->updateProductTotalStock();
        });

        static::deleted(function ($productStock) {
            $productStock->updateProductTotalStock();
        });
    }

    /**
     * Recalculate and update total stock in products table.
     */
    public function updateProductTotalStock()
    {
        $totalStock = self::where('product_id', $this->product_id)->sum('stock');
        
        // Disable timestamps updates on Product when updating total stock
        $product = $this->product;
        if ($product) {
            $product->timestamps = false;
            $product->update(['stock' => $totalStock]);
        }
    }
}
