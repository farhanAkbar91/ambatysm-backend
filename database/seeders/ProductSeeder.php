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
                'description' => "Kemeja polos lengan panjang warna putih dengan bahan katun premium yang adem dan mudah disetrika. Sangat cocok untuk acara formal maupun kerja sehari-hari.",
                'price' => 199000,
                'stock' => 50,
                'image' => 'https://placehold.co/400x600/eeeeee/31343C?text=Kemeja+Putih',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Jaket Denim Vintage',
                'description' => "Jaket denim bergaya klasik/vintage dengan aksen washed yang keren. Terbuat dari bahan denim tebal berkualitas tinggi, awet, dan nyaman dipakai cuaca dingin.",
                'price' => 349000,
                'stock' => 25,
                'image' => 'https://placehold.co/400x600/eeeeee/31343C?text=Jaket+Denim',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Kaos Basic Hitam',
                'description' => "Kaos katun combed 30s premium warna hitam pekat. Potongan regular fit yang santai, menyerap keringat dengan baik, dan tidak mudah melar.",
                'price' => 89000,
                'stock' => 100,
                'image' => 'https://placehold.co/400x600/eeeeee/31343C?text=Kaos+Hitam',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Celana Jeans Slim Fit',
                'description' => "Celana jeans potongan slim fit dengan bahan stretch (melar) yang mengikuti bentuk kaki namun tetap fleksibel bergerak. Warna biru tua klasik.",
                'price' => 275000,
                'stock' => 40,
                'image' => 'https://placehold.co/400x600/eeeeee/31343C?text=Celana+Jeans',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Cardigan Rajut Wanita',
                'description' => "Cardigan rajut rajutan halus wanita dengan kancing depan. Model oversize yang aesthetic dan hangat untuk gaya kasual sehari-hari.",
                'price' => 210000,
                'stock' => 30,
                'image' => 'https://placehold.co/400x600/eeeeee/31343C?text=Cardigan+Rajut',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Totebag Kanvas Estetik',
                'description' => "Totebag bahan kanvas tebal dengan penutup resleting dan kantong dalam. Desain minimalis yang cocok untuk kuliah, kerja, maupun belanja santai.",
                'price' => 65000,
                'stock' => 80,
                'image' => 'https://placehold.co/400x600/eeeeee/31343C?text=Totebag+Kanvas',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Kemeja Flanel Kotak-kotak',
                'description' => "Kemeja motif kotak-kotak bahan flanel lembut. Dapat dijadikan kemeja biasa atau sebagai outer kasual. Kombinasi warna merah dan hitam.",
                'price' => 185000,
                'stock' => 45,
                'image' => 'https://placehold.co/400x600/eeeeee/31343C?text=Kemeja+Flanel',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Sneakers Putih Kasual',
                'description' => "Sepatu sneakers warna putih bersih dengan desain minimalis modern. Dilengkapi sol karet anti-selip dan insole empuk untuk kenyamanan sepanjang hari.",
                'price' => 450000,
                'stock' => 20,
                'image' => 'https://placehold.co/400x600/eeeeee/31343C?text=Sneakers+Putih',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Topi Baseball Hitam',
                'description' => "Topi baseball warna hitam polos dengan strap pengatur ukuran di belakang. Melindungi kepala dari panas terik sekaligus menambah aksen santai gaya Anda.",
                'price' => 55000,
                'stock' => 60,
                'image' => 'https://placehold.co/400x600/eeeeee/31343C?text=Topi+Baseball',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Jam Tangan Minimalis',
                'description' => "Jam tangan analog pria/wanita dengan tali kulit hitam dan dial putih bersih yang mewah. Tahan percikan air ringan dan sangat elegan.",
                'price' => 299000,
                'stock' => 15,
                'image' => 'https://placehold.co/400x600/eeeeee/31343C?text=Jam+Tangan',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Celana Chino Cream',
                'description' => "Celana panjang model chino warna cream/beige terbuat dari katun twill berkualitas. Nyaman dipakai seharian untuk aktivitas semi-formal dan kasual.",
                'price' => 195000,
                'stock' => 35,
                'image' => 'https://placehold.co/400x600/eeeeee/31343C?text=Celana+Chino',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Sweater Hoodie Abu-abu',
                'description' => "Sweater hoodie dengan kantong kanguru depan dan penutup kepala serut. Bahan fleece tebal yang sangat hangat namun tidak panas di kulit.",
                'price' => 225000,
                'stock' => 50,
                'image' => 'https://placehold.co/400x600/eeeeee/31343C?text=Sweater+Hoodie',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Kaos Oversized Putih',
                'description' => "Kaos putih polos dengan potongan oversized/boxy fit yang kekinian. Terbuat dari katun combed 24s yang lebih tebal dan kokoh di tubuh.",
                'price' => 115000,
                'stock' => 70,
                'image' => 'https://placehold.co/400x600/eeeeee/31343C?text=Kaos+Oversized',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Celana Cargo Olive Green',
                'description' => "Celana panjang kargo warna hijau olive dengan saku samping yang fungsional. Terbuat dari bahan ripstop premium yang kuat untuk petualangan Anda.",
                'price' => 245000,
                'stock' => 30,
                'image' => 'https://placehold.co/400x600/eeeeee/31343C?text=Celana+Cargo',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Jaket Bomber Hitam',
                'description' => "Jaket bomber warna hitam dengan bahan taslan waterproof ringan dan furing katun yang nyaman. Sangat keren untuk berkendara motor atau outfit malam.",
                'price' => 389000,
                'stock' => 20,
                'image' => 'https://placehold.co/400x600/eeeeee/31343C?text=Jaket+Bomber',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Sepatu Loafers Kulit',
                'description' => "Sepatu loafers slip-on pria berbahan kulit sintetis premium dengan finishing mengkilap. Cocok dipadukan dengan celana chino atau celana bahan formal.",
                'price' => 520000,
                'stock' => 15,
                'image' => 'https://placehold.co/400x600/eeeeee/31343C?text=Sepatu+Loafers',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Kemeja Batik Modern',
                'description' => "Kemeja batik lengan pendek bermotif geometris modern dengan warna dasar hitam-emas. Menggunakan bahan katun prima berlapis furing halus.",
                'price' => 235000,
                'stock' => 25,
                'image' => 'https://placehold.co/400x600/eeeeee/31343C?text=Kemeja+Batik',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Celana Pendek Santai',
                'description' => "Celana pendek kasual santai bahan katun baby terry dengan karet pinggang elastis dan tali serut. Ideal untuk bersantai di rumah atau liburan ke pantai.",
                'price' => 95000,
                'stock' => 50,
                'image' => 'https://placehold.co/400x600/eeeeee/31343C?text=Celana+Pendek',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Hoodie Hitam Minimalis',
                'description' => "Hoodie warna hitam pekat tanpa motif dengan siluet drop-shoulder yang modis. Bahan katun terry premium berkualitas tinggi.",
                'price' => 260000,
                'stock' => 40,
                'image' => 'https://placehold.co/400x600/eeeeee/31343C?text=Hoodie+Hitam',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Kaos Polo Navy Blue',
                'description' => "Kaos polo lengan pendek warna biru dongker terbuat dari bahan lacoste katun piqué berpori halus. Sangat rapi namun tetap kasual.",
                'price' => 135000,
                'stock' => 60,
                'image' => 'https://placehold.co/400x600/eeeeee/31343C?text=Kaos+Polo',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Kemeja Oversized Wanita',
                'description' => "Kemeja oversized wanita lengan panjang dengan potongan loose fit. Terbuat dari bahan linen crinkle yang ringan, jatuh, dan dingin dipakai.",
                'price' => 180000,
                'stock' => 35,
                'image' => 'https://placehold.co/400x600/eeeeee/31343C?text=Kemeja+Oversized',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Celana Kulot Hitam',
                'description' => "Celana kulot wanita potongan lebar berbahan scuba elastis tebal. Memiliki saku kanan-kiri dan karet pinggang belakang yang nyaman.",
                'price' => 165000,
                'stock' => 45,
                'image' => 'https://placehold.co/400x600/eeeeee/31343C?text=Celana+Kulot',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Jaket Parka Outdoor',
                'description' => "Jaket parka pria bermotif militaristik dengan banyak saku luar. Dilengkapi hoodie bongkar-pasang dan bahan luar kanvas drill windbreaker.",
                'price' => 420000,
                'stock' => 15,
                'image' => 'https://placehold.co/400x600/eeeeee/31343C?text=Jaket+Parka',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Sandal Slide Kasual',
                'description' => "Sandal slide kasual yang enteng dengan sol empuk ergonomis berkontur anatomi kaki. Tahan air dan sangat praktis untuk bepergian santai.",
                'price' => 120000,
                'stock' => 50,
                'image' => 'https://placehold.co/400x600/eeeeee/31343C?text=Sandal+Slide',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Tas Ransel Waterproof',
                'description' => "Tas punggung ransel berkapasitas 25 liter dengan kompartemen laptop 15.6 inci berlapis busa pengaman. Bahan luar nylon cordura waterproof.",
                'price' => 310000,
                'stock' => 20,
                'image' => 'https://placehold.co/400x600/eeeeee/31343C?text=Tas+Ransel',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($products as $pData) {
            $stock = $pData['stock'];
            unset($pData['stock']);

            $product = \App\Models\Product::create($pData);

            // Bagikan total stock produk ke dalam variasi ukuran dan warna secara dinamis
            $sizes = ['S', 'M', 'L', 'XL'];
            $colors = ['Hitam', 'Putih', 'Abu-abu'];

            $remainingStock = $stock;
            foreach ($colors as $color) {
                foreach ($sizes as $size) {
                    if ($remainingStock <= 0) break 2;
                    $variantStock = min(rand(5, 15), $remainingStock);
                    if ($variantStock > 0) {
                        \App\Models\ProductStock::create([
                            'product_id' => $product->id,
                            'size' => $size,
                            'color' => $color,
                            'stock' => $variantStock,
                        ]);
                        $remainingStock -= $variantStock;
                    }
                }
            }

            if ($remainingStock > 0) {
                \App\Models\ProductStock::create([
                    'product_id' => $product->id,
                    'size' => 'M',
                    'color' => 'Hitam',
                    'stock' => $remainingStock,
                ]);
            }
        }
    }
}