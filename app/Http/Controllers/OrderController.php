<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    // Checkout dari Cart (Regular Order) dengan logika stok
    public function checkout(Request $request)
    {   
        $request->validate([
        'shipping_address' => 'required|string',
        'city_id' => 'required|string',
        'courier' => 'required|string',
        'shipping_cost' => 'required|numeric'
    ]);

        // Mulai transaksi di awal agar lockin bekerja
        DB::beginTransaction();

        try {
            // Ambil data keranjang user
            $carts = Cart::with('product')->where('user_id', $request->user()->id)->get();

            if ($carts->isEmpty()) {
                return response()->json(['message' => 'Keranjang anda kosong'], 400);
            }

            $totalAmount = 0;

            // 1. Validasi Stok dan Lock Baris Produk di Database
            foreach ($carts as $cart) {
                // lockForUpdate() mecegah user lain mengubsh stok produk ini sampai transaksi ini selesai
                $product = Product::where('id', $cart->product_id)->lockForUpdate()->first();

                if (!$product) {
                    DB::rollBack();
                    return response()->json(['message' => 'Produk tidak ditemukan'], 404);
                }

                if ($product->stock < $cart->quantity) {
                    DB::rollBack();
                    return response()->json([
                        'message' => 'Maaf, stok tidak mencukupi untuk produk: ' . $product->name . '. Sisa stok: ' . $product->stock
                    ], 400);
                }

                // Hitung total harga sekalian menggunakan harga terbaru
                $totalAmount += $product->price * $cart->quantity;
            }

            // 2. Buat Data Order (Kepala Transaksi)
            $order = Order::create([
                'user_id' => $request->user()->id,
                'total_amount' => $totalAmount + $request->shipping_cost, // Total harga barang + Ongkir
                'type' => 'regular',
                'status' => 'pending',
                'shipping_address' => $request->shipping_address,
                'city_id' => $request->city_id,
                'courier' => $request->courier,
                'shipping_cost' => $request->shipping_cost
            ]);

            // 3. Buat Detail Order & Kurangi
            foreach ($carts as $cart) {
                $product = Product::find($cart->product_id); // Ambil lagi produk yang sudah dilock

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cart->product_id,
                    'quantity' => $cart->quantity,
                    'price' => $product->price, // Snapshot harga saat dibeli
                ]);

                // Kurangi stok di database
                $product->decrement('stock', $cart->quantity);
            }

            // 4. Kosongkan Keranjang
            Cart::where('user_id', $request->user()->id)->delete();

            // 5. Simpan semua perubahan ke database
            DB::commit();

            return response()->json([
                'message' => 'Checkout berhasil! Stok telah diperbarui.',
                'order' => $order, 
            ], 201);

        } catch (\Exception $e) {
            // Jika terjadi error di tengah jalan, batalkan semua progres (stok tidak jadi berkurang, order dibatalkan)
            DB::rollback();
            return response()->json(['message' => 'Checkout gagal terjadi kesalahan sistem', 'error' => $e->getMessage()], 500);
        }
    }

    // Endpoin Khusus Custom Order
    public function customOrder(Request $request)
    {
        // 1. Validasi input dari user
        $request->validate([
            'custom_notes' => 'required|string',
            'custom_image' => 'nullable|image|mimes:jpeg,png,jpg,pdf|max:2048' // Format gambar yang diterima, max 2 MB
        ]);

        $imagePath = null;

        // 2. Cek apakah user mengunggah gambar/desain refrensi
        if ($request->hasFile('custom_image')) {
            // Simpan file ke folder storage/app/public/custom_orders
            $imagePath = $request->file('custom_image')->store('custom_orders', 'public');
        }

        // 3. Simpan data request ke database
        // Harga di-set 0 karena menunggu review dan estimasi dari Admin
        $order = Order::create([
            'user_id' => $request->user()->id,
            'total_amount' => 0,
            'type' => 'custom',
            'custom_notes' => $request->custom_notes,
            'custom_image' => $imagePath,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Request custom order berhasil dikirim! Silakan tunggu admin mereview dan memberikan harga estimasi.',
            'order' => $order,
        ], 201);
    }

    // Konfirmasi Pembayaran
    public function confirmPayment(Request $request, $id)
    {
        $request->validate([
            'payment_method' => 'required|string',
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048' //Maks 2MB
        ]);

        $order = Order::where('user_id', $request->user()->id)->findOrFail($id);

        if ($request->hasFile('payment_proof')) {
            $path = $request->file('payment_proof')->store('payments', 'public');

            $order->update([
                'payment_method' => $request->payment_method,
                'payment_proof' => $path,
                'status' => 'waiting_confirmation'
            ]);

            return response()->json(['message' => 'Payment proof uploaded successfully', 'order' => $order]);
        }

        return response()->json(['message' => 'Failed to upload image'], 400);
    }

    public function index(Request $request)
    {
        // Hanya mengambil pesanan milik user yang login
        $orders = Order::with('items.product')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json($orders);
    }
}
