<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ShippingController extends Controller
{
    // 1. Data Kota Palsu (Mock)
    public function getCities()
    {
        $dummyCities = [
            ["city_id" => "1", "city_name" => "Jakarta Selatan", "type" => "Kota"],
            ["city_id" => "2", "city_name" => "Surabaya", "type" => "Kota"],
            ["city_id" => "3", "city_name" => "Bandung", "type" => "Kota"],
            ["city_id" => "282", "city_name" => "Mojokerto", "type" => "Kota"],
            ["city_id" => "5", "city_name" => "Sidoarjo", "type" => "Kabupaten"]
        ];

        return response()->json([
            'status' => 'success',
            'data' => $dummyCities
        ]);
    }

    // 2. Data Ongkir Palsu (Mock)
    public function checkCost(Request $request)
    {
        $request->validate([
            'destination' => 'required|numeric', 
            'weight' => 'required|numeric', 
            'courier' => 'required|string' 
        ]);

        // Struktur JSON ini dibuat persis seperti balasan asli RajaOngkir
        $dummyResponse = [
            "rajaongkir" => [
                "results" => [
                    [
                        "code" => $request->courier,
                        "name" => strtoupper($request->courier),
                        "costs" => [
                            [
                                "service" => "REG (Reguler Dummy)",
                                "description" => "Layanan Reguler",
                                "cost" => [
                                    [
                                        "value" => 15000,
                                        "etd" => "2-3",
                                        "note" => ""
                                    ]
                                ]
                            ],
                            [
                                "service" => "YES (Yakin Esok Sampai Dummy)",
                                "description" => "Layanan Cepat",
                                "cost" => [
                                    [
                                        "value" => 25000,
                                        "etd" => "1-1",
                                        "note" => ""
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return response()->json($dummyResponse);
    }
}