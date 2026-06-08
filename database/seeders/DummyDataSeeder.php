<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Dummy Customers
        $customers = [];
        for ($i = 1; $i <= 15; $i++) {
            $user = User::firstOrCreate(
                ['email' => "dummycustomer{$i}@test.com"],
                [
                    'name' => "Customer {$i}",
                    'password' => Hash::make('password123'),
                    'role' => 'customer'
                ]
            );
            $customers[] = $user;
        }

        // Ensure we have products
        $products = Product::all();
        if ($products->isEmpty()) {
            // Need some dummy products if DB is empty
            for ($i = 1; $i <= 5; $i++) {
                $products->push(Product::create([
                    'name' => "Produk Dummy {$i}",
                    'price' => rand(50000, 300000),
                    'stock' => rand(50, 200),
                    'image' => null
                ]));
            }
        }

        // 2. Create Dummy Orders & Items for the past 6 months
        // Let's create around 100 orders
        $statuses = ['completed', 'completed', 'completed', 'completed', 'completed', 'processing', 'cancelled', 'paid', 'waiting_confirmation'];
        
        for ($i = 0; $i < 100; $i++) {
            $user = $customers[array_rand($customers)];
            
            // Random date between Jan 1 2026 and Jun 7 2026
            $start = Carbon::create(2026, 1, 1)->timestamp;
            $end = Carbon::create(2026, 6, 7)->timestamp;
            $randomDate = Carbon::createFromTimestamp(rand($start, $end));

            $orderStatus = $statuses[array_rand($statuses)];
            $numItems = rand(1, 3);
            
            $totalAmount = 0;
            $itemsData = [];

            for ($j = 0; $j < $numItems; $j++) {
                $product = $products->random();
                $qty = rand(1, 4);
                $price = $product->price;
                $totalAmount += ($price * $qty);
                
                $itemsData[] = [
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'price' => $price,
                ];
            }

            $shippingCost = rand(10000, 50000);
            $totalAmount += $shippingCost;

            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => $totalAmount,
                'type' => 'regular',
                'status' => $orderStatus,
                'shipping_address' => "Jalan Dummy No {$i}, Kota Test",
                'city_id' => '1',
                'courier' => 'jne',
                'shipping_cost' => $shippingCost,
                'created_at' => $randomDate,
                'updated_at' => $randomDate,
            ]);

            foreach ($itemsData as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }

            // Create rating review if order is completed
            if ($orderStatus === 'completed') {
                // 80% chance to write a review
                if (rand(1, 10) <= 8) {
                    $ratings = [
                        5 => [
                            'Sangat puas dengan kualitas bahannya! Sangat nyaman dipakai seharian.',
                            'Ukuran pas sekali, jahitan rapi. Pengiriman cepat.',
                            'Bagus banget, sesuai deskripsi. Recommended seller!',
                            'Warnanya bagus banget dan bahan adem. Bakal beli lagi.',
                            'Kualitas premium! Tidak mengecewakan sama sekali.',
                            'Mantap, pas di badan dan bahan sangat halus.'
                        ],
                        4 => [
                            'Bahan bagus, ukuran agak pas tapi masih nyaman.',
                            'Pengiriman agak lama tapi produknya memuaskan.',
                            'Bagus sekali sesuai harga, recommended.',
                            'Desainnya keren, cuman bahannya agak tipis dikit tapi oke lah.',
                            'Sesuai dengan gambar, jahitan lumayan rapi.',
                            'Cukup memuaskan, kualitas sepadan dengan harganya.'
                        ],
                        3 => [
                            'Biasa saja, bahan standar sesuai harga.',
                            'Ukuran agak kekecilan dibanding deskripsi, tapi barang sampai aman.',
                            'Lumayan lah buat sehari-hari.',
                            'Warna agak berbeda sedikit dari foto, tapi masih oke.',
                            'Pelayanan cepat, tapi kualitas produk standar.',
                            'Biasa aja, gak terlalu bagus dan gak terlalu jelek.'
                        ],
                        2 => [
                            'Kurang puas, jahitan kurang rapi dan ukuran kekecilan.',
                            'Bahan tipis sekali dan warna agak pudar.',
                            'Model kurang sesuai dengan foto produk.',
                            'Respon chat penjual lambat, kualitas produk kurang memuaskan.'
                        ],
                        1 => [
                            'Sangat kecewa, ukuran tidak sesuai dan ada noda di pakaian.',
                            'Barang rusak/cacat saat diterima dan respon admin lambat.',
                            'Bahan panas dan jahitan gampang lepas.',
                            'Salah kirim barang dan proses pengembalian ribet.'
                        ]
                    ];

                    $rand = rand(1, 100);
                    if ($rand <= 55) {
                        $rating = 5;
                    } elseif ($rand <= 80) {
                        $rating = 4;
                    } elseif ($rand <= 92) {
                        $rating = 3;
                    } elseif ($rand <= 97) {
                        $rating = 2;
                    } else {
                        $rating = 1;
                    }

                    $comments = $ratings[$rating];
                    $comment = $comments[array_rand($comments)];

                    \App\Models\Review::create([
                        'user_id' => $user->id,
                        'order_id' => $order->id,
                        'rating' => $rating,
                        'comment' => $comment,
                        'created_at' => $randomDate->copy()->addDays(rand(1, 3)),
                        'updated_at' => $randomDate->copy()->addDays(rand(1, 3)),
                    ]);
                }
            }
        }
    }
}
