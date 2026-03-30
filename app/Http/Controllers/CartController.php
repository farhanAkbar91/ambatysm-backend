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
        ]);

        $product = \App\Models\Product::find($request->product_id);
    
        $cartItem = Cart::where('user_id', $request->user()->id)
                        ->where('product_id', $request->product_id)
                        ->first();
        
        // Hitung total kuantitas yang diminta (yang sudah ada di keranjang + yang baru ditambahkan)
        $requestedQuantity = $cartItem ? $cartItem->quantity + $request->quantity : $request->quantity;

        // Tolak jika melebihi stok
        if ($requestedQuantity > $product->stock) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kuantitas melebihi stok yang tersedia (' . $product->stock . ')'
            ], 400); // 400 Bad Request
        }
        
        if ($cartItem) {
            $cartItem->increment('quantity', $request->quantity);
        } else {
            Cart::create([
                'user_id' => $request->user()->id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity
            ]);
        }

        return response()->json(['message' => 'Product added to cart']);
    }

    public function removeFromCart(Request $request, $id)
    {
        Cart::where('user_id', $request->user()->id)->where('id', $id)->delete();
        return response()->json(['message' => 'Item removed from cart']);
    }
}
