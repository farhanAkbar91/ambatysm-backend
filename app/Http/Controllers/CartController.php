<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cart = Cart::with('product.stocks')->where('user_id', $request->user()->id)->get();
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
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'size' => 'nullable|string',
            'color' => 'nullable|string',
        ]);
        
        $cart = \App\Models\Cart::where('id', $id)->where('user_id', auth()->id())->first();
        
        if (!$cart) return response()->json(['message' => 'Not found'], 404);

        $size = $request->input('size', $cart->size);
        $color = $request->input('color', $cart->color);
        $quantity = $request->input('quantity', $cart->quantity);

        // Cari stok varian spesifik
        $variantStock = \App\Models\ProductStock::where('product_id', $cart->product_id)
            ->where('size', $size)
            ->where('color', $color)
            ->first();

        $availableStock = $variantStock ? $variantStock->stock : 0;

        if ($quantity > $availableStock) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kuantitas melebihi stok ' . $size . ' - ' . $color . ' yang tersedia (' . $availableStock . ')'
            ], 400);
        }

        // Cek apakah ada item lain dengan varian yang sama
        $existingCartItem = \App\Models\Cart::where('user_id', auth()->id())
            ->where('product_id', $cart->product_id)
            ->where('size', $size)
            ->where('color', $color)
            ->where('id', '!=', $id)
            ->first();

        if ($existingCartItem) {
            $newQuantity = $existingCartItem->quantity + $quantity;
            if ($newQuantity > $availableStock) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Total kuantitas produk gabungan melebihi stok yang tersedia (' . $availableStock . ')'
                ], 400);
            }
            $existingCartItem->update(['quantity' => $newQuantity]);
            $cart->delete();
        } else {
            $cart->update([
                'size' => $size,
                'color' => $color,
                'quantity' => $quantity,
            ]);
        }

        return response()->json(['message' => 'Updated successfully']);
    }
}
