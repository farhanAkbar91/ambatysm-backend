<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cart = Cart::with('product')->where('user_id', $request->user()->id)->get();
        return response()->json([
        'status' => 'success',
        'data' => $cart
    ]);
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'size' => 'nullable|string',
            'color' => 'nullable|string',
        ]);

        $size = $request->size;
        $color = $request->color;

        // Auto-resolve jika size atau color kosong (misal ditambah dari beranda)
        if (empty($size) || empty($color)) {
            $firstVariant = \App\Models\ProductStock::where('product_id', $request->product_id)
                ->where('stock', '>', 0)
                ->first();
            
            if (!$firstVariant) {
                $firstVariant = \App\Models\ProductStock::where('product_id', $request->product_id)->first();
            }

            if ($firstVariant) {
                $size = $firstVariant->size;
                $color = $firstVariant->color;
            } else {
                $size = 'All Size';
                $color = 'Default';
            }
        }

        $product = \App\Models\Product::find($request->product_id);
    
        $cartItem = Cart::where('user_id', $request->user()->id)
                        ->where('product_id', $request->product_id)
                        ->where('size', $size)
                        ->where('color', $color)
                        ->first();
        
        $requestedQuantity = $cartItem ? $cartItem->quantity + $request->quantity : $request->quantity;

        // Cari stok varian spesifik
        $variantStock = \App\Models\ProductStock::where('product_id', $request->product_id)
            ->where('size', $size)
            ->where('color', $color)
            ->first();

        $availableStock = $variantStock ? $variantStock->stock : 0;

        // Tolak jika melebihi stok varian
        if ($requestedQuantity > $availableStock) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kuantitas melebihi stok ' . $size . ' - ' . $color . ' yang tersedia (' . $availableStock . ')'
            ], 400);
        }
        
        if ($cartItem) {
            $cartItem->increment('quantity', $request->quantity);
        } else {
            Cart::create([
                'user_id' => $request->user()->id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'size' => $size,
                'color' => $color
            ]);
        }

        return response()->json(['message' => 'Product added to cart']);
    }

    public function removeFromCart(Request $request, $id)
    {
        Cart::where('user_id', $request->user()->id)->where('id', $id)->delete();
        return response()->json(['message' => 'Item removed from cart']);
    }

    public function updateQuantity(Request $request, $id)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);
        
        // Sesuaikan nama model 'Cart' dengan yang kamu gunakan
        $cart = \App\Models\Cart::where('id', $id)->where('user_id', auth()->id())->first();
        
        if (!$cart) return response()->json(['message' => 'Not found'], 404);

        $cart->update(['quantity' => $request->quantity]);
        return response()->json(['message' => 'Updated successfully']);
    }
}
