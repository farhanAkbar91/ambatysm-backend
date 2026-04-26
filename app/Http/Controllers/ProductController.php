<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Mengambil semua data dari tabel products
        // (Bisa gunakan ::latest()->get() jika ingin yang terbaru di atas)
        $products = \App\Models\Product::all(); 

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil mengambil data produk',
            'data' => $products // Masukkan variabel $products ke sini
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validasi data yang dikirim dari Frontend
        $request->validate([
            'name'  => 'required|string|max:255',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048', // Maksimal 2MB
        ]);

        // 2. Proses Upload Gambar
        $imagePath = null;
        if ($request->hasFile('image')) {
            // Simpan file ke folder storage/app/public/products
            $path = $request->file('image')->store('products', 'public');
            
            // Buat URL agar bisa diakses langsung oleh tag <img> di HTML
            $imagePath = '/storage/' . $path;
        }

        // 3. Simpan Data ke Database
        $product = \App\Models\Product::create([
            'name'  => $request->name,
            'price' => $request->price,
            'stock' => $request->stock,
            'image' => $imagePath,
        ]);

        // 4. Kembalikan Response JSON Sukses
        return response()->json([
            'status'  => 'success',
            'message' => 'Produk baru berhasil ditambahkan!',
            'data'    => $product
        ], 201); // 201 adalah kode HTTP untuk "Created"
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        // Return data product dalam format JSON agar bisa dibaca oleh script.js
        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil mengambil detail produk',
            'data' => $product
        ]);
    }

   /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        // 1. Validasi data (image dibuat 'nullable' karena saat edit, foto tidak wajib diganti)
        $request->validate([
            'name'  => 'required|string|max:255',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', 
        ]);

        // 2. Siapkan array data yang akan diupdate
        $updateData = [
            'name'  => $request->name,
            'price' => $request->price,
            'stock' => $request->stock,
        ];

        // 3. Jika dosen/asisten upload foto baru, simpan dan timpa path fotonya
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $updateData['image'] = '/storage/' . $path;
        }

        // 4. Simpan perubahan ke Database
        $product->update($updateData);

        // 5. Kembalikan Response JSON Sukses
        return response()->json([
            'status'  => 'success',
            'message' => 'Produk berhasil diperbarui!',
            'data'    => $product
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // Hapus data dari database
        $product->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Produk berhasil dihapus dari etalase'
        ]);
    }
}
