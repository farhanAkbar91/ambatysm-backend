<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\ShippingController;

// --- ROUTE PUBLIK ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/products', [ProductController::class, 'index']); // Semua orang bisa lihat katalog
Route::get('/products/{product}', [ProductController::class, 'show']); // Semua orang bisa lihat detail produk

// --- ROUTE KHUSUS USER LOGIN (Customer & Admin bisa masuk) ---
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'me']);
    Route::get('/orders', [OrderController::class, 'index']);
    
    Route::post('/logout', [AuthController::class, 'logout']);

    // Cart (Keranjang)
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/add', [CartController::class, 'addToCart']);
    Route::delete('/cart/remove/{id}', [CartController::class, 'removeFromCart']);
    Route::put('/cart/update/{id}', [CartController::class, 'updateQuantity']);

    // Transaksi
    Route::post('/checkout', [OrderController::class, 'checkout']);
    Route::post('/custom-order', [OrderController::class, 'customOrder']);
    Route::post('/orders/{id}/confirm-payment', [OrderController::class, 'confirmPayment']);

    // Integrasi Ongkir
    Route::get('/shipping/cities', [ShippingController::class, 'getCities']);
    Route::post('/shipping/cost', [ShippingController::class, 'checkCost']);
});

// --- ROUTE KHUSUS ADMIN (Gembok Ganda: Harus Login + Role Admin) ---
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    // Manajemen Produk
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{product}', [ProductController::class, 'update']);
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);
    
    // Manajemen Pesanan
    Route::get('/admin/orders', [AdminOrderController::class, 'index']);
    Route::patch('/admin/orders/{id}/status', [AdminOrderController::class, 'updateStatus']);
    Route::patch('/admin/orders/{id}/custom-price', [AdminOrderController::class, 'reviewCustomOrder']);
});
