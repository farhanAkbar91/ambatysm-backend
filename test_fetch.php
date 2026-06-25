<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Http;

$response = Http::withoutVerifying()->get('https://raw.githubusercontent.com/69dev69dev/indonesia-geo-db/main/city.json');

if ($response->successful()) {
    $data = $response->json();
    echo "Fetched successful! Total cities: " . count($data) . "\n";
    echo "First item:\n";
    print_r($data[0] ?? $data);
} else {
    echo "Fetch failed. Status: " . $response->status() . "\n";
}
