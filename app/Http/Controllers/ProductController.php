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
        // Mengambil semua data dari tabel products dengan rata-rata rating dan jumlah ulasan beserta variasinya
        $products = \App\Models\Product::withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->with('stocks')
            ->get(); 

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil mengambil data produk',
            'data' => $products
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validasi data yang dikirim dari Frontend
        $request->validate([
            'name'     => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'price'    => 'required|numeric',
            'stock'    => 'nullable|integer',
            'image'    => 'required|image|mimes:jpeg,png,jpg,webp|max:2048', // Maksimal 2MB
        ]);

        // 2. Proses Upload Gambar
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = \App\Helpers\ImageUploader::upload($request->file('image'), 'products');
        }

        // 3. Simpan Data ke Database
        $product = \App\Models\Product::create([
            'name'     => $request->name,
            'category' => $request->category,
            'price'    => $request->price,
            'stock'    => 0, // Akan dihitung dari varian
            'image'    => $imagePath,
        ]);

        // 4. Simpan Varian Stok jika ada
        if ($request->has('variants')) {
            $variants = json_decode($request->variants, true);
            if (is_array($variants)) {
                foreach ($variants as $v) {
                    if (!empty($v['size']) && !empty($v['color'])) {
                        $product->stocks()->create([
                            'size' => $v['size'],
                            'color' => $v['color'],
                            'stock' => intval($v['stock'] ?? 0),
                        ]);
                    }
                }
            }
        } else {
            // Fallback untuk request lama
            $product->stocks()->create([
                'size' => 'All Size',
                'color' => 'Default',
                'stock' => intval($request->input('stock', 0)),
            ]);
        }

        // 5. Kembalikan Response JSON Sukses
        return response()->json([
            'status'  => 'success',
            'message' => 'Produk baru berhasil ditambahkan!',
            'data'    => $product->load('stocks')
        ], 201); // 201 adalah kode HTTP untuk "Created"
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        // Memuat rata-rata rating, jumlah ulasan, dan detail ulasan berserta user pembuatnya
        $product->loadAvg('reviews', 'rating');
        $product->loadCount('reviews');
        $product->load(['reviews' => function ($query) {
            $query->with('user')->latest();
        }, 'stocks']);

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
            'name'     => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'price'    => 'required|numeric',
            'stock'    => 'nullable|integer',
            'image'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', 
        ]);

        // 2. Siapkan array data yang akan diupdate
        $updateData = [
            'name'     => $request->name,
            'category' => $request->category,
            'price'    => $request->price,
        ];

        // 3. Jika dosen/asisten upload foto baru, simpan dan timpa path fotonya
        if ($request->hasFile('image')) {
            $updateData['image'] = \App\Helpers\ImageUploader::upload($request->file('image'), 'products');
        }

        // 4. Simpan perubahan ke Database
        $product->update($updateData);

        // 5. Simpan Varian Stok jika ada
        if ($request->has('variants')) {
            $variants = json_decode($request->variants, true);
            if (is_array($variants)) {
                // Hapus stok varian lama
                $product->stocks()->delete();

                foreach ($variants as $v) {
                    if (!empty($v['size']) && !empty($v['color'])) {
                        $product->stocks()->create([
                            'size' => $v['size'],
                            'color' => $v['color'],
                            'stock' => intval($v['stock'] ?? 0),
                        ]);
                    }
                }
            }
        } else if ($request->has('stock')) {
            // Fallback request lama
            $product->stocks()->updateOrCreate(
                ['size' => 'All Size', 'color' => 'Default'],
                ['stock' => intval($request->stock)]
            );
        }

        // 6. Kembalikan Response JSON Sukses
        return response()->json([
            'status'  => 'success',
            'message' => 'Produk berhasil diperbarui!',
            'data'    => $product->load('stocks')
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
