<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    // 1. Lihat semua pesanan masuk (Bisa difilter)
    public function index()
    {
        // Menarik data order beserta nama pembeli dan detail barangnya
        $orders = Order::with(['items.product'])->latest()->get();
        return response()->json($orders);
    }

    // 2. Update status order (Misal: validasi bukti transfer)
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,waiting_confirmation,paid,processing,completed,cancelled'
        ]);

        // Ambil data order beserta detail itemnya
        $order = Order::with('items')->findOrFail($id);
        
        $oldStatus = $order->status;
        $newStatus = $request->status;

        // LOGIKA PENGEMBALIAN STOK
        // Kita hanya mengembalikan stok jika status berubah JADI 'cancelled' 
        // dan status sebelumnya BUKAN 'cancelled' (mencegah duplikasi pengembalian)
        if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
            foreach ($order->items as $item) {
                // Tambahkan kembali stok ke produk terkait
                $product = \App\Models\Product::find($item->product_id);
                if ($product) {
                    $product->increment('stock', $item->quantity);
                }
            }
        }

        // Update status di database
        $order->update(['status' => $newStatus]);

        return response()->json([
            'message' => 'Status pesanan berhasil diperbarui.',
            'current_status' => $newStatus,
            'stock_restored' => ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') ? true : false
        ]);
    }

    // 3. Review Custom Order & Tetapkan Harga Estimasi
    public function reviewCustomOrder(Request $request, $id)
    {
        $request->validate([
            'estimated_price' => 'required|numeric|min:1',
            'status' => 'required|in:review,diterima,ditolak,dikerjakan' // Sesuai desain awal Anda
        ]);

        $order = Order::where('type', 'custom')->findOrFail($id);
        
        $order->update([
            'total_amount' => $request->estimated_price,
            'status' => $request->status
        ]);

        return response()->json([
            'message' => 'Harga estimasi custom order berhasil ditetapkan.',
            'order' => $order
        ]);
    }
}
