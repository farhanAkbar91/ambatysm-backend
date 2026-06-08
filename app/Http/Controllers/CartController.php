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

        $product = \App\Models\Product::find($request->product_id);
    
        $cartItem = Cart::where('user_id', $request->user()->id)
                        ->where('product_id', $request->product_id)
                        ->where('size', $request->size)
                        ->where('color', $request->color)
                        ->first();
        
        // Hitung total kuantitas yang diminta (yang sudah ada di keranjang + yang baru ditambahkan)
        $requestedQuantity = $cartItem ? $cartItem->quantity + $request->quantity : $request->quantity;

        // Cari stok varian spesifik
        $variantStock = \App\Models\ProductStock::where('product_id', $request->product_id)
            ->where('size', $request->size)
            ->where('color', $request->color)
            ->first();

        $availableStock = $variantStock ? $variantStock->stock : 0;

        // Tolak jika melebihi stok varian
        if ($requestedQuantity > $availableStock) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kuantitas melebihi stok ' . ($request->size ?? '') . ' - ' . ($request->color ?? '') . ' yang tersedia (' . $availableStock . ')'
            ], 400); // 400 Bad Request
        }
        
        if ($cartItem) {
            $cartItem->increment('quantity', $request->quantity);
        } else {
            Cart::create([
                'user_id' => $request->user()->id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'size' => $request->size,
                'color' => $request->color
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
