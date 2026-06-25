<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\ProductStock;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $products = [
            // --- TOPS ---
            [
                'name' => 'Oversized Tee',
                'description' => "Kaos oversized bergaya kasual dengan bahan katun combed premium yang adem dan menyerap keringat. Potongan boxy fit modern yang sangat nyaman digunakan untuk beraktivitas sehari-hari.",
                'price' => 119000,
                'stock' => 120,
                'image' => 'https://www.image2url.com/r2/default/images/1782320248973-aad9eb23-04c6-4817-a683-fd0568efad09.webp',
                'colors' => ['Hitam', 'Putih', 'Abu-abu', 'Sage Green'],
                'sizes' => ['S', 'M', 'L', 'XL'],
            ],
            [
                'name' => 'Fitted Tee',
                'description' => "Kaos fitted tee dengan potongan pas di badan. Terbuat dari bahan katun stretch lembut yang adem dan nyaman untuk daily wear.",
                'price' => 99000,
                'stock' => 90,
                'image' => 'https://www.image2url.com/r2/default/images/1782321679148-9399ffde-0438-4a31-91af-c173bced748d.webp',
                'colors' => ['Hitam', 'Putih', 'Navy'],
                'sizes' => ['S', 'M', 'L', 'XL'],
            ],
            [
                'name' => 'Sleeveless Top',
                'description' => "Atasan sleeveless kasual tanpa lengan. Sangat cocok sebagai inner blazer, outer, atau langsung dipakai saat cuaca hangat.",
                'price' => 89000,
                'stock' => 80,
                'image' => 'https://www.image2url.com/r2/default/images/1782321867312-b0d67561-e157-49c5-afa9-76d3674c9e4d.webp',
                'colors' => ['Putih', 'Hitam', 'Cream'],
                'sizes' => ['S', 'M', 'L'],
            ],
            [
                'name' => 'Long sleeve shirt',
                'description' => "Kemeja lengan panjang polos dengan bahan katun premium. Rapi dan elegan untuk kebutuhan formal, ngantor, maupun semi-formal.",
                'price' => 199000,
                'stock' => 75,
                'image' => 'https://www.image2url.com/r2/default/images/1782320512596-1210b6fd-6e9d-4b75-970b-9dd7a3b26635.webp',
                'colors' => ['Putih', 'Biru Muda', 'Hitam'],
                'sizes' => ['M', 'L', 'XL'],
            ],
            [
                'name' => 'Short sleeve shirt',
                'description' => "Kemeja lengan pendek kasual santai dari bahan katun berkualitas tinggi. Potongan loose yang sejuk and modis untuk hang out maupun kerja santai.",
                'price' => 179000,
                'stock' => 85,
                'image' => 'https://www.image2url.com/r2/default/images/1782320586504-5f6f4ca3-6169-4baa-ae6d-576c39d73934.webp',
                'colors' => ['Olive', 'Navy', 'Maroon'],
                'sizes' => ['M', 'L', 'XL'],
            ],
            [
                'name' => 'Basic Hoodie',
                'description' => "Sweater hoodie lengan panjang dilengkapi tudung serut. Terbuat dari bahan fleece tebal bertekstur lembut dan hangat.",
                'price' => 249000,
                'stock' => 60,
                'image' => 'https://www.image2url.com/r2/default/images/1782320629644-3167cb32-92af-4cc1-bd5d-fdb7e754e944.webp',
                'colors' => ['Hitam', 'Abu-abu', 'Navy'],
                'sizes' => ['M', 'L', 'XL'],
            ],
            [
                'name' => 'Polo shirt',
                'description' => "Kaos polo berkerah dengan bahan katun piqué berkualitas. Pilihan tepat untuk gaya kasual yang rapi dan trendi.",
                'price' => 149000,
                'stock' => 100,
                'image' => 'https://www.image2url.com/r2/default/images/1782320733336-88cdfe75-be5a-4958-a434-3490a12b8050.webp',
                'colors' => ['Navy', 'Hitam', 'Merah'],
                'sizes' => ['S', 'M', 'L', 'XL'],
            ],

            // --- Outerwear ---
            [
                'name' => 'Trucker jacket',
                'description' => "Jaket model trucker dengan bahan kanvas tangguh yang stylish dan timeless. Dilengkapi saku dada fungsional.",
                'price' => 329000,
                'stock' => 40,
                'image' => 'https://www.image2url.com/r2/default/images/1782321494618-3181e303-0df5-46a1-9d95-2210eb6da5aa.webp',
                'colors' => ['Khaki', 'Hitam', 'Hijau Army'],
                'sizes' => ['M', 'L', 'XL'],
            ],
            [
                'name' => 'Bomber shin jacket',
                'description' => "Jaket bomber bersiluet modern dengan bahan polyester tahan angin. Sangat cocok dipakai saat berkendara malam hari.",
                'price' => 349000,
                'stock' => 45,
                'image' => 'https://www.image2url.com/r2/default/images/1782321555927-a1ab80cc-66dc-474a-a7df-a2654c2f704b.webp',
                'colors' => ['Hitam', 'Navy', 'Hijau Army'],
                'sizes' => ['M', 'L', 'XL'],
            ],
            [
                'name' => 'Canvas jacket',
                'description' => "Jaket kasual berbahan canvas berkualitas premium yang tebal dan tahan lama. Desain minimalis namun tetap fungsional.",
                'price' => 299000,
                'stock' => 50,
                'image' => 'https://www.image2url.com/r2/default/images/1782320804517-f6b5a461-8924-4d92-b7f6-c028a6358bd5.webp',
                'colors' => ['Cokelat', 'Hitam'],
                'sizes' => ['M', 'L', 'XL'],
            ],

            // --- BOTTOMS ---
            [
                'name' => 'Short relax pants',
                'description' => "Celana pendek santai bertali pinggang elastis. Dibuat dari bahan katun baby terry yang adem dan sangat leluasa untuk bergerak.",
                'price' => 99000,
                'stock' => 110,
                'image' => 'https://www.image2url.com/r2/default/images/1782320847062-5b08232e-2d3e-450f-8b6a-7eb78f6eebd3.webp',
                'colors' => ['Hitam', 'Abu-abu', 'Navy'],
                'sizes' => ['S', 'M', 'L', 'XL'],
            ],
            [
                'name' => 'Slim fit jeans',
                'description' => "Celana panjang denim berpotongan slim fit yang stretchable. Memberikan siluet kaki jenjang dan kenyamanan gerak ekstra.",
                'price' => 289000,
                'stock' => 70,
                'image' => 'https://www.image2url.com/r2/default/images/1782320930969-40dcfa3d-d23a-42c0-a783-ac18c879030c.webp',
                'colors' => ['Biru Tua', 'Hitam', 'Biru Muda'],
                'sizes' => ['28', '30', '32', '34'],
            ],
            [
                'name' => 'Regular fit jeans',
                'description' => "Celana jeans klasik potongan regular fit bermutu tinggi. Tebal, tangguh, dan longgar untuk kebebasan gerak maksimal sehari-hari.",
                'price' => 279000,
                'stock' => 65,
                'image' => 'https://www.image2url.com/r2/default/images/1782321347025-e276b2cb-462a-4084-9c38-ca1680886131.webp',
                'colors' => ['Biru Tua', 'Hitam'],
                'sizes' => ['28', '30', '32', '34'],
            ],
            [
                'name' => 'Relax fit pants',
                'description' => "Celana panjang santai berpotongan lebar (loose fit). Menggunakan bahan linen campuran yang ringan dan sangat adem.",
                'price' => 199000,
                'stock' => 80,
                'image' => 'https://www.image2url.com/r2/default/images/1782320987594-e5f0b05c-9ab4-441b-a148-ce467d672975.webp',
                'colors' => ['Cream', 'Hitam', 'Abu-abu'],
                'sizes' => ['S', 'M', 'L', 'XL'],
            ],
            [
                'name' => 'Ankle pants',
                'description' => "Celana panjang model ankle fit bermotif polos minimalis. Sangat elegan untuk ke kantor maupun dipadukan dengan kaos santai.",
                'price' => 189000,
                'stock' => 75,
                'image' => 'https://www.image2url.com/r2/default/images/1782321107659-134e3e6c-632a-4d5d-9fa4-6ed5e7d348ce.webp',
                'colors' => ['Hitam', 'Navy', 'Cream'],
                'sizes' => ['M', 'L', 'XL'],
            ],
            [
                'name' => 'Short chino pants',
                'description' => "Celana pendek chino bahan katun twill stretch. Modis, trendi, dan memiliki tampilan semi-formal yang praktis.",
                'price' => 119000,
                'stock' => 95,
                'image' => 'https://www.image2url.com/r2/default/images/1782321233715-2fdcdbbc-f78c-4562-955e-9198f9ed76af.webp',
                'colors' => ['Beige', 'Hitam', 'Navy'],
                'sizes' => ['28', '30', '32', '34'],
            ],
            [
                'name' => 'Active jogger pants',
                'description' => "Celana jogger active yang sangat fleksibel dan menyerap keringat. Ideal untuk gym, jogging, maupun hangout kasual.",
                'price' => 169000,
                'stock' => 85,
                'image' => 'https://www.image2url.com/r2/default/images/1782321261781-7865cd68-e2e8-43bd-864b-1fa7bfc2b6a2.webp',
                'colors' => ['Abu-abu', 'Hitam', 'Navy'],
                'sizes' => ['S', 'M', 'L', 'XL'],
            ],
        ];

        // Kosongkan tabel produk secara paksa.
        Product::query()->forceDelete();

        foreach ($products as $pData) {
            $stock = $pData['stock'];
            $colors = $pData['colors'];
            $sizes = $pData['sizes'];
            
            unset($pData['stock']);
            unset($pData['colors']);
            unset($pData['sizes']);

            $pData['created_at'] = $now;
            $pData['updated_at'] = $now;

            $product = Product::create($pData);

            // Bagikan total stock produk ke dalam variasi ukuran dan warna secara merata
            $numVariants = count($sizes) * count($colors);
            $baseStock = floor($stock / $numVariants);
            $remainder = $stock % $numVariants;

            foreach ($colors as $color) {
                foreach ($sizes as $size) {
                    $variantStock = $baseStock;
                    if ($remainder > 0) {
                        $variantStock += 1;
                        $remainder--;
                    }

                    ProductStock::create([
                        'product_id' => $product->id,
                        'size' => $size,
                        'color' => $color,
                        'stock' => $variantStock,
                    ]);
                }
            }

            // Perbarui total stock produk secara manual karena model events dinonaktifkan (WithoutModelEvents) saat seeding
            $product->timestamps = false;
            $product->update(['stock' => $stock]);
        }
    }
}