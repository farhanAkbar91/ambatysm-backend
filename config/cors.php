<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'], 

    // Masukkan domain Vercel dan lokal kamu di sini:
    'allowed_origins' => [
        'https://ambatysm-frontend-vue.vercel.app',
        'http://localhost:5173', // Port standar Vite lokal
        'http://localhost:3000', // Jaga-jaga jika lokal pakai port lain
    ], 

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'], 

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true, // Tetap 'true' karena domain di atas sudah spesifik

];
