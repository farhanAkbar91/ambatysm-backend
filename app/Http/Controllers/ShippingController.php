<?php

namespace App\Http\Controllers;

use App\Models\Province;
use App\Models\City;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    /**
     * Mengambil daftar semua provinsi.
     */
    public function getProvinces()
    {
        $provinces = Province::orderBy('name')->get();

        return response()->json([
            'status' => 'success',
            'data' => $provinces
        ]);
    }

    /**
     * Mengambil daftar kota, bisa difilter berdasarkan provinsi.
     */
    public function getCities(Request $request)
    {
        $provinceId = $request->query('province_id');
        $query = City::query();

        if ($provinceId) {
            $query->where('province_id', $provinceId);
        }

        $cities = $query->orderBy('name')->get()->map(function ($city) {
            return [
                'city_id' => $city->id,
                'province_id' => $city->province_id,
                'city_name' => $city->name,
                'type' => $city->type
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $cities
        ]);
    }

    /**
     * Menghitung ongkos kirim tiruan (mock) dari Kota Surabaya ke kota tujuan.
     */
    public function checkCost(Request $request)
    {
        $request->validate([
            'destination' => 'required|string', 
            'weight' => 'required|numeric', 
            'courier' => 'required|string' 
        ]);

        $destination = $request->input('destination');
        $city = City::find($destination);

        if (!$city) {
            // Fallback default
            $regPrice = 15000;
            $yesPrice = 25000;
            $regEtd = '2-3';
            $yesEtd = '1-2';
        } else {
            $provId = (int) $city->province_id;
            $isKota = strtolower($city->type) === 'kota';

            // Tentukan biaya dasar berdasarkan zona provinsi (asal: Surabaya, Jawa Timur)
            if ($provId === 35) { // Jawa Timur
                $baseReg = 8000;
                $baseYes = 15000;
                $regEtd = $isKota ? '1-2' : '2-3';
                $yesEtd = '1-1';
            } elseif (in_array($provId, [31, 32, 33, 34, 36])) { // Pulau Jawa Lainnya (Jakarta, Jabar, Jateng, DIY, Banten)
                $baseReg = 14000;
                $baseYes = 24000;
                $regEtd = '2-3';
                $yesEtd = '1-2';
            } elseif (in_array($provId, [51, 52])) { // Bali & NTB
                $baseReg = 18000;
                $baseYes = 28000;
                $regEtd = '2-4';
                $yesEtd = '1-2';
            } elseif ($provId >= 11 && $provId <= 21) { // Sumatera
                $baseReg = 25000;
                $baseYes = 42000;
                $regEtd = '3-5';
                $yesEtd = '1-2';
            } elseif ($provId >= 61 && $provId <= 65) { // Kalimantan
                $baseReg = 28000;
                $baseYes = 45000;
                $regEtd = '3-5';
                $yesEtd = '1-2';
            } elseif ($provId >= 71 && $provId <= 76) { // Sulawesi
                $baseReg = 30000;
                $baseYes = 48000;
                $regEtd = '3-5';
                $yesEtd = '1-2';
            } else { // Papua, Maluku, NTT, dan wilayah timur lainnya
                $baseReg = 45000;
                $baseYes = 75000;
                $regEtd = '4-7';
                $yesEtd = '2-3';
            }

            // Tambahan biaya jika Kabupaten (karena lokasi lebih terpencil dibanding Kota)
            if (!$isKota) {
                $baseReg += 3000;
                $baseYes += 5000;
            }

            // Kalikan dengan berat dalam KG (asumsi berat minimal 1kg, input weight dalam gram)
            $weightInKg = ceil($request->input('weight', 1000) / 1000);
            if ($weightInKg < 1) {
                $weightInKg = 1;
            }

            $regPrice = $baseReg * $weightInKg;
            $yesPrice = $baseYes * $weightInKg;
        }

        $courierCode = strtolower($request->courier);
        $courierName = strtoupper($request->courier);

        // Struktur respon persis seperti struktur format balasan RajaOngkir
        $response = [
            "rajaongkir" => [
                "results" => [
                    [
                        "code" => $courierCode,
                        "name" => $courierName,
                        "costs" => [
                            [
                                "service" => "REG",
                                "description" => "Layanan Reguler",
                                "cost" => [
                                    [
                                        "value" => $regPrice,
                                        "etd" => $regEtd,
                                        "note" => ""
                                    ]
                                ]
                            ],
                            [
                                "service" => "YES",
                                "description" => "Layanan Cepat (Yakin Esok Sampai)",
                                "cost" => [
                                    [
                                        "value" => $yesPrice,
                                        "etd" => $yesEtd,
                                        "note" => ""
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return response()->json($response);
    }
}