<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CustomerController;

// Landing Page (Public)
Route::get('/', function () {
    return view('landing');
})->name('landing');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Product Management
    Route::get('/products', [AdminController::class, 'products'])->name('products');
    Route::get('/products/create', [AdminController::class, 'createProduct'])->name('products.create');
    Route::post('/products', [AdminController::class, 'storeProduct'])->name('products.store');
    Route::get('/products/{product}/edit', [AdminController::class, 'editProduct'])->name('products.edit');
    Route::put('/products/{product}', [AdminController::class, 'updateProduct'])->name('products.update');
    Route::delete('/products/{product}', [AdminController::class, 'deleteProduct'])->name('products.delete');
    
    // Customer Management (CRM)
    Route::get('/customers', [AdminController::class, 'customers'])->name('customers');
    Route::put('/customers/{user}/loyalty', [AdminController::class, 'updateLoyalty'])->name('customers.loyalty');
    Route::put('/customers/{user}/message', [AdminController::class, 'updateMessage'])->name('customers.message');
    
    // Order Management
    Route::get('/orders', [AdminController::class, 'orders'])->name('orders');
    Route::get('/orders/{order}', [AdminController::class, 'showOrder'])->name('orders.show');
    Route::put('/orders/{order}/status', [AdminController::class, 'updateOrderStatus'])->name('orders.status');
});

// Customer Routes
Route::middleware(['auth', 'customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/home', [CustomerController::class, 'home'])->name('home');
    
    // Shopping Cart
    Route::post('/cart/add', [CustomerController::class, 'addToCart'])->name('cart.add');
    Route::put('/cart/update', [CustomerController::class, 'updateCart'])->name('cart.update');
    Route::delete('/cart/remove', [CustomerController::class, 'removeFromCart'])->name('cart.remove');
    Route::get('/cart', [CustomerController::class, 'showCart'])->name('cart.show');
    
    // Checkout
    Route::get('/checkout', [CustomerController::class, 'checkout'])->name('checkout');
    Route::post('/checkout', [CustomerController::class, 'processCheckout'])->name('checkout.process');
    
    // Orders
    Route::get('/orders', [CustomerController::class, 'orders'])->name('orders');
    Route::get('/orders/{order}', [CustomerController::class, 'showOrder'])->name('orders.show');
    Route::post('/orders/{orderItem}/review', [CustomerController::class, 'submitReview'])->name('orders.review');
});
