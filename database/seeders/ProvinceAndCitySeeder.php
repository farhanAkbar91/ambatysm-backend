<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use App\Models\Province;
use App\Models\City;

class ProvinceAndCitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data lama jika ada
        City::query()->forceDelete();
        Province::query()->forceDelete();

        $response = Http::withoutVerifying()->retry(3, 200)->get('https://emsifa.github.io/api-wilayah-indonesia/api/provinces.json');
        
        if ($response->successful()) {
            $provinces = $response->json();
            
            foreach ($provinces as $prov) {
                Province::create([
                    'id' => $prov['id'],
                    'name' => ucwords(strtolower($prov['name'])),
                ]);

                // Fetch regencies (cities/kabupaten) for this province
                $regResponse = Http::withoutVerifying()->retry(3, 200)->get("https://emsifa.github.io/api-wilayah-indonesia/api/regencies/{$prov['id']}.json");
                
                if ($regResponse->successful()) {
                    $regencies = $regResponse->json();
                    
                    foreach ($regencies as $reg) {
                        $rawName = $reg['name'];
                        
                        if (str_starts_with($rawName, 'KOTA ')) {
                            $type = 'Kota';
                            $name = ucwords(strtolower(substr($rawName, 5)));
                        } elseif (str_starts_with($rawName, 'KABUPATEN ')) {
                            $type = 'Kabupaten';
                            $name = ucwords(strtolower(substr($rawName, 10)));
                        } else {
                            $type = 'Kabupaten';
                            $name = ucwords(strtolower($rawName));
                        }

                        City::create([
                            'id' => $reg['id'],
                            'province_id' => $prov['id'],
                            'name' => $name,
                            'type' => $type,
                        ]);
                    }
                }
            }
        }
    }
}
