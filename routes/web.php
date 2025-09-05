<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CrmController;

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
    Route::post('/products', [AdminController::class, 'storeProduct'])->name('products.store');
    Route::put('/products/{product}', [AdminController::class, 'updateProduct'])->name('products.update');
    Route::delete('/products/{product}', [AdminController::class, 'deleteProduct'])->name('products.delete');
    Route::get('/api/products/{product}', [AdminController::class, 'getProduct'])->name('api.products.show');
    
    // Customer Management (CRM)
    Route::get('/customers', [AdminController::class, 'customers'])->name('customers');
    Route::post('/customers', [AdminController::class, 'storeCustomer'])->name('customers.store');
    Route::put('/customers/{user}', [AdminController::class, 'updateCustomer'])->name('customers.update');
    Route::put('/customers/{user}/loyalty', [AdminController::class, 'updateLoyalty'])->name('customers.loyalty');
    Route::put('/customers/{user}/message', [AdminController::class, 'updateMessage'])->name('customers.message');
    
    // Order Management
    Route::get('/orders', [AdminController::class, 'orders'])->name('orders');
    Route::post('/orders', [AdminController::class, 'storeOrder'])->name('orders.store');
    Route::get('/orders/{order}', [AdminController::class, 'showOrder'])->name('orders.show');
    Route::put('/orders/{order}/status', [AdminController::class, 'updateOrderStatus'])->name('orders.status');
    Route::get('/api/customers', [AdminController::class, 'getCustomers'])->name('api.customers');
    Route::get('/api/products', [AdminController::class, 'getProducts'])->name('api.products');
    
    // Reports
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
    
    // CRM Management
    Route::get('/crm', [CrmController::class, 'index'])->name('crm.index');
    Route::post('/crm/{user}/message', [CrmController::class, 'sendMessage'])->name('crm.sendMessage');
    Route::post('/crm/{user}/discount', [CrmController::class, 'setDiscount'])->name('crm.setDiscount');
    Route::get('/crm/products', [CrmController::class, 'getProducts'])->name('crm.getProducts');
    Route::post('/crm/preview-message', [CrmController::class, 'previewDiscountMessage'])->name('crm.previewMessage');
    Route::get('/crm/{user}/discounts', [CrmController::class, 'getCustomerDiscounts'])->name('crm.getCustomerDiscounts');
    Route::delete('/crm/discount/{discount}', [CrmController::class, 'removeDiscount'])->name('crm.removeDiscount');
    
    // Debug route for CRM functionality
    Route::get('/crm/debug', function() {
        try {
            $users = App\Models\User::where('role', 'customer')->take(3)->get();
            $products = App\Models\Product::take(3)->get();
            $discounts = App\Models\PersonalDiscount::take(5)->get();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'users_count' => $users->count(),
                    'products_count' => $products->count(),
                    'discounts_count' => $discounts->count(),
                    'sample_user' => $users->first(),
                    'sample_product' => $products->first(),
                    'database_connection' => 'OK'
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    })->name('crm.debug');
});

// Customer Routes
Route::middleware(['auth', 'customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/home', [CustomerController::class, 'home'])->name('home');
    
    // Notifications
    Route::get('/notifications', [CustomerController::class, 'notifications'])->name('customer.notifications.index');
    Route::post('/notifications/mark-read', [CustomerController::class, 'markNotificationRead'])->name('customer.notifications.mark-read');
    Route::post('/notifications/mark-all-read', [CustomerController::class, 'markAllNotificationsRead'])->name('customer.notifications.mark-all-read');
    Route::get('/notifications/data', [CustomerController::class, 'getNotificationsData'])->name('customer.notifications.data');
    
    // Profile Editing
    Route::get('/profile/edit', [CustomerController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profile/update', [CustomerController::class, 'updateProfile'])->name('profile.update');
    
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
