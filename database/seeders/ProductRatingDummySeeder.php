<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Review;
use App\Models\City;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class ProductRatingDummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Get or create customers
        $customers = User::where('role', 'customer')->get();
        if ($customers->count() < 15) {
            for ($i = $customers->count() + 1; $i <= 15; $i++) {
                User::firstOrCreate(
                    ['email' => "dummycustomer{$i}@test.com"],
                    [
                        'name' => "Customer {$i}",
                        'password' => Hash::make('password123'),
                        'role' => 'customer'
                    ]
                );
            }
            $customers = User::where('role', 'customer')->get();
        }

        // 2. Get active products
        $products = Product::whereNull('deleted_at')->get();
        if ($products->isEmpty()) {
            $this->command->info('Tidak ada produk aktif untuk diberi rating.');
            return;
        }

        // 3. Get cities
        $cities = City::all();

        // 4. Define ratings and realistic Indonesian comments
        $ratingsComments = [
            5 => [
                'Sangat puas dengan kualitas bahannya! Sangat nyaman dipakai seharian.',
                'Ukuran pas sekali, jahitan rapi. Pengiriman cepat.',
                'Bagus banget, sesuai deskripsi. Recommended seller!',
                'Warnanya bagus banget dan bahan adem. Bakal beli lagi.',
                'Kualitas premium! Tidak mengecewakan sama sekali.',
                'Mantap, pas di badan dan bahan sangat halus.',
                'Produk original, packing aman sekali. Terima kasih seller.',
                'Desainnya modern dan kualitas kainnya tebal. Sangat suka!',
                'Sangat recommended. Pelayanan responsif dan barang bagus.',
                'Realpict! Bahannya adem dan nyaman dipakai.'
            ],
            4 => [
                'Bahan bagus, ukuran agak pas tapi masih nyaman.',
                'Pengiriman agak lama tapi produknya memuaskan.',
                'Bagus sekali sesuai harga, recommended.',
                'Desainnya keren, cuman bahannya agak tipis dikit tapi oke lah.',
                'Sesuai dengan gambar, jahitan lumayan rapi.',
                'Cukup memuaskan, kualitas sepadan dengan harganya.',
                'Bagus, cuman warna aslinya sedikit lebih gelap dari foto.',
                'Pengemasan rapi dan aman, kualitas produk oke banget.',
                'Ukuran sesuai deskripsi, bahannya lumayan adem.',
                'Respon penjual baik dan pengiriman cepat.'
            ],
            3 => [
                'Biasa saja, bahan standar sesuai harga.',
                'Ukuran agak kekecilan dibanding deskripsi, tapi barang sampai aman.',
                'Lumayan lah buat sehari-hari.',
                'Warna agak berbeda sedikit dari foto, tapi masih oke.',
                'Pelayanan cepat, tapi kualitas produk standar.',
                'Biasa aja, gak terlalu bagus dan gak terlalu jelek.',
                'Jahitan kurang rapi di beberapa bagian, tapi overall oke.',
                'Pengiriman agak lambat, kualitas produk biasa saja.',
                'Bahannya agak tipis, tapi pas di badan.',
                'Standard lah untuk harga segini.'
            ],
            2 => [
                'Kurang puas, jahitan kurang rapi dan ukuran kekecilan.',
                'Bahan tipis sekali dan warna agak pudar.',
                'Model kurang sesuai dengan foto produk.',
                'Respon chat penjual lambat, kualitas produk kurang memuaskan.',
                'Kain agak kasar dan gampang lecek.',
                'Ukuran tidak pas dan ada benang yang terlepas.'
            ],
            1 => [
                'Sangat kecewa, ukuran tidak sesuai dan ada noda di pakaian.',
                'Barang rusak/cacat saat diterima dan respon admin lambat.',
                'Bahan panas dan jahitan gampang lepas.',
                'Salah kirim barang dan proses pengembalian ribet.',
                'Kualitas sangat jelek tidak sesuai ekspektasi.',
                'Barang tidak dikirim lengkap dan admin tidak responsif.'
            ]
        ];

        $couriers = ['jne', 'jnt', 'tiki', 'sicepat', 'pos'];
        $paymentMethods = ['tf_bank', 'qris', 'gopay', 'ovo'];

        $this->command->info("Memulai seeding ulasan untuk " . $products->count() . " produk...");

        foreach ($products as $product) {
            // Seed 5 to 10 reviews for each product to build a healthy distribution
            $numReviews = rand(5, 10);

            for ($k = 0; $k < $numReviews; $k++) {
                $user = $customers->random();

                // Get a random variant for size and color from product stocks
                $variants = $product->stocks;
                if ($variants && $variants->isNotEmpty()) {
                    $variant = $variants->random();
                    $size = $variant->size;
                    $color = $variant->color;
                } else {
                    $size = 'All Size';
                    $color = 'Default';
                }

                // Random date in the last 4 months
                $randomDate = Carbon::now()->subDays(rand(1, 120));

                // Mostly generate positive ratings
                $rand = rand(1, 100);
                if ($rand <= 60) {
                    $rating = 5;
                } elseif ($rand <= 85) {
                    $rating = 4;
                } elseif ($rand <= 94) {
                    $rating = 3;
                } elseif ($rand <= 97) {
                    $rating = 2;
                } else {
                    $rating = 1;
                }

                $comments = $ratingsComments[$rating];
                $comment = $comments[array_rand($comments)];

                $shippingCost = rand(10000, 30000);
                $qty = rand(1, 2);
                $totalAmount = ($product->price * $qty) + $shippingCost;

                // 1. Create a completed Order
                $order = Order::create([
                    'user_id' => $user->id,
                    'total_amount' => $totalAmount,
                    'type' => 'regular',
                    'status' => 'completed',
                    'shipping_address' => "Jalan Kenangan No. " . rand(1, 150) . ", Kota Test",
                    'city_id' => $cities->isNotEmpty() ? $cities->random()->id : '1101',
                    'courier' => $couriers[array_rand($couriers)],
                    'shipping_cost' => $shippingCost,
                    'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                    'created_at' => $randomDate,
                    'updated_at' => $randomDate,
                ]);

                // 2. Create the OrderItem mapping the product
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'price' => $product->price,
                    'size' => $size,
                    'color' => $color,
                    'created_at' => $randomDate,
                    'updated_at' => $randomDate,
                ]);

                // 3. Create the Review
                Review::create([
                    'user_id' => $user->id,
                    'order_id' => $order->id,
                    'rating' => $rating,
                    'comment' => $comment,
                    'created_at' => $randomDate->copy()->addDays(rand(1, 3)),
                    'updated_at' => $randomDate->copy()->addDays(rand(1, 3)),
                ]);
            }
        }

        $this->command->info("Seeding ulasan selesai!");
    }
}
