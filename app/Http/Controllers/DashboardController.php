<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function getStats(Request $request)
    {
        $bulan = $request->query('bulan');
        $tahun = $request->query('tahun');
        $tanggal = $request->query('tanggal');

        // Base query for orders
        $query = Order::query();

        if ($tahun && $tahun !== 'all') {
            $query->whereYear('created_at', $tahun);
        }
        if ($bulan && $bulan !== 'all') {
            $query->whereMonth('created_at', $bulan);
        }
        if ($tanggal && $tanggal !== 'all') {
            $query->whereDay('created_at', $tanggal);
        }

        // Clone base query for different metrics
        $valPendapatan = (clone $query)->where('status', 'completed')->sum('total_amount');
        $valTransaksi = (clone $query)->count();
        $valBatal = (clone $query)->where('status', 'cancelled')->count();
        $valCustomer = (clone $query)->distinct('user_id')->count('user_id');

        // Products sold (only from completed orders)
        $completedOrderIds = (clone $query)->where('status', 'completed')->pluck('id');
        $valProduk = OrderItem::whereIn('order_id', $completedOrderIds)->sum('quantity');

        // Chart Data (Group by Day or Month)
        // If specific month is selected, group by day (1-31).
        $chartDataPenjualan = array_fill(0, 31, 0);
        $chartDataTransaksi = array_fill(0, 31, 0);

        if ($bulan !== 'all' && $tahun !== 'all') {
            $ordersInMonth = (clone $query)->select(
                DB::raw('DAY(created_at) as day'),
                DB::raw('SUM(total_amount) as total_penjualan'),
                DB::raw('COUNT(*) as total_transaksi')
            )->groupBy('day')->get();

            foreach ($ordersInMonth as $o) {
                // array index is 0-30 for day 1-31
                if ($o->day >= 1 && $o->day <= 31) {
                    $chartDataPenjualan[$o->day - 1] = $o->total_penjualan;
                    $chartDataTransaksi[$o->day - 1] = $o->total_transaksi;
                }
            }
        }

        // Top 3 Products
        // Find most sold products in this period (or overall)
        $topProductsQuery = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_sold'))
            ->whereHas('order', function ($q) use ($tahun, $bulan, $tanggal) {
                $q->where('status', 'completed');
                if ($tahun && $tahun !== 'all') $q->whereYear('created_at', $tahun);
                if ($bulan && $bulan !== 'all') $q->whereMonth('created_at', $bulan);
                if ($tanggal && $tanggal !== 'all') $q->whereDay('created_at', $tanggal);
            })
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->take(3)
            ->get();

        $topProducts = [];
        foreach ($topProductsQuery as $tp) {
            $product = Product::find($tp->product_id);
            if ($product) {
                $product->total_sold = $tp->total_sold;
                $topProducts[] = $product;
            }
        }

        return response()->json([
            'valPendapatan' => $valPendapatan,
            'valTransaksi' => $valTransaksi,
            'valProduk' => (int)$valProduk,
            'valBatal' => $valBatal,
            'valCustomer' => $valCustomer,
            'chartDataPenjualan' => $chartDataPenjualan,
            'chartDataTransaksi' => $chartDataTransaksi,
            'topProducts' => $topProducts
        ]);
    }
}
