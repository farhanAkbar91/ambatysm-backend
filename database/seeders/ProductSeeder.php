<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $now = Carbon::now();

        $products = [
            [
                'name' => 'Kemeja Putih Polos Pria',
                'price' => 199000,
                'stock' => 50,
                'image' => 'https://placehold.co/400x600/eeeeee/31343C?text=Kemeja+Putih',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Jaket Denim Vintage',
                'price' => 349000,
                'stock' => 25,
                'image' => 'https://placehold.co/400x600/eeeeee/31343C?text=Jaket+Denim',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Kaos Basic Hitam',
                'price' => 89000,
                'stock' => 100,
                'image' => 'https://placehold.co/400x600/eeeeee/31343C?text=Kaos+Hitam',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Celana Jeans Slim Fit',
                'price' => 275000,
                'stock' => 40,
                'image' => 'https://placehold.co/400x600/eeeeee/31343C?text=Celana+Jeans',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Cardigan Rajut Wanita',
                'price' => 210000,
                'stock' => 30,
                'image' => 'https://placehold.co/400x600/eeeeee/31343C?text=Cardigan+Rajut',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Totebag Kanvas Estetik',
                'price' => 65000,
                'stock' => 80,
                'image' => 'https://placehold.co/400x600/eeeeee/31343C?text=Totebag+Kanvas',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Kemeja Flanel Kotak-kotak',
                'price' => 185000,
                'stock' => 45,
                'image' => 'https://placehold.co/400x600/eeeeee/31343C?text=Kemeja+Flanel',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Sneakers Putih Kasual',
                'price' => 450000,
                'stock' => 20,
                'image' => 'https://placehold.co/400x600/eeeeee/31343C?text=Sneakers+Putih',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Topi Baseball Hitam',
                'price' => 55000,
                'stock' => 60,
                'image' => 'https://placehold.co/400x600/eeeeee/31343C?text=Topi+Baseball',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Jam Tangan Minimalis',
                'price' => 299000,
                'stock' => 15,
                'image' => 'https://placehold.co/400x600/eeeeee/31343C?text=Jam+Tangan',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Celana Chino Cream',
                'price' => 195000,
                'stock' => 35,
                'image' => 'https://placehold.co/400x600/eeeeee/31343C?text=Celana+Chino',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Sweater Hoodie Abu-abu',
                'price' => 225000,
                'stock' => 50,
                'image' => 'https://placehold.co/400x600/eeeeee/31343C?text=Sweater+Hoodie',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('products')->insert($products);
    }
}