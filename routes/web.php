<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\AdminCustomerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderChatController;
use Illuminate\Support\Facades\Route;

// ══════════════════════════════════════════════════════════════════════════════
// PUBLIC
// ══════════════════════════════════════════════════════════════════════════════

Route::get('/', fn () => view('home'))->name('home');

// ══════════════════════════════════════════════════════════════════════════════
// GUEST ONLY  (redirect to home if already logged in)
// ══════════════════════════════════════════════════════════════════════════════

Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',   [AuthController::class, 'login'])->name('login.post');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',[AuthController::class, 'register'])->name('register.post');
});

// ══════════════════════════════════════════════════════════════════════════════
// LOGOUT  (any authenticated user)
// ══════════════════════════════════════════════════════════════════════════════

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ══════════════════════════════════════════════════════════════════════════════
// ADMIN  (/admin/...)
// ══════════════════════════════════════════════════════════════════════════════

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

        // Driver approval
        Route::post('/drivers/{driver}/approve', [AdminController::class, 'approveDriver'])->name('driver.approve');
        Route::post('/drivers/{driver}/reject',  [AdminController::class, 'rejectDriver'])->name('driver.reject');

        // Stores
        Route::get('/stores',                [AdminController::class, 'stores'])->name('stores');
        Route::get('/stores/create',         [AdminController::class, 'storeCreate'])->name('stores.create');
        Route::post('/stores',               [AdminController::class, 'storeStore'])->name('stores.store');
        Route::get('/stores/{store}/edit',   [AdminController::class, 'storeEdit'])->name('stores.edit');
        Route::put('/stores/{store}',        [AdminController::class, 'storeUpdate'])->name('stores.update');
        Route::delete('/stores/{store}',     [AdminController::class, 'storeDestroy'])->name('stores.destroy');

        // Menus Management (scoped by Store)
        Route::prefix('/stores/{store}/menus')->name('stores.menus.')->group(function () {
            Route::get('/',              [MenuController::class, 'index'])->name('index');
            Route::get('/create',        [MenuController::class, 'create'])->name('create');
            Route::post('/',             [MenuController::class, 'store'])->name('store');
            Route::get('/{menu}/edit',   [MenuController::class, 'edit'])->name('edit');
            Route::put('/{menu}',        [MenuController::class, 'update'])->name('update');
            Route::delete('/{menu}',     [MenuController::class, 'destroy'])->name('destroy');
        });

        // Orders Management
        Route::prefix('/orders')->name('orders.')->group(function () {
            Route::get('/',                     [AdminOrderController::class, 'index'])->name('index');
            Route::get('/{order}',              [AdminOrderController::class, 'show'])->name('show');
            Route::post('/{order}/status',      [AdminOrderController::class, 'updateStatus'])->name('update-status');
        });

        // Jumlah pesanan per customer
        Route::get('/customers', [AdminCustomerController::class, 'index'])->name('customers.index');

    });

// ══════════════════════════════════════════════════════════════════════════════
// DRIVER  (/driver/...)
// ══════════════════════════════════════════════════════════════════════════════

// Pending page — accessible to all authenticated drivers (incl. pending status)
Route::get('/driver/pending', [DriverController::class, 'pending'])
    ->middleware(['auth', 'role:driver'])
    ->name('driver.pending');

// Active-driver-only routes
Route::middleware(['auth', 'role:driver'])
    ->prefix('driver')
    ->name('driver.')
    ->group(function () {
        Route::get('/',                              [DriverController::class, 'dashboard'])->name('dashboard');
        Route::post('/toggle-status',                [DriverController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/set-status',                   [DriverController::class, 'setStatus'])->name('set-status');
        Route::post('/orders/{order}/take',          [DriverController::class, 'takeOrder'])->name('take-order');
        Route::post('/orders/{order}/update-status', [DriverController::class, 'updateOrderStatus'])->name('update-status');
        Route::get('/history',                       [DriverController::class, 'history'])->name('history');
    });

// ══════════════════════════════════════════════════════════════════════════════
// CUSTOMER  (/customer/...)
// ══════════════════════════════════════════════════════════════════════════════

Route::middleware(['auth', 'role:customer'])
    ->prefix('customer')
    ->name('customer.')
    ->group(function () {
        Route::get('/',                          [CustomerController::class, 'dashboard'])->name('dashboard');
        Route::get('/custom-order',              [CustomerController::class, 'createCustomOrder'])->name('custom-order.create');
        Route::post('/custom-order',             [CustomerController::class, 'storeCustomOrder'])->name('custom-order.store');
        Route::get('/stores/{store}',            [CustomerController::class, 'storeDetail'])->name('store');
        Route::post('/stores/{store}/order',     [CustomerController::class, 'placeOrder'])->name('order.place');
        Route::get('/orders',                    [CustomerController::class, 'orders'])->name('orders');
        Route::get('/orders/{order}',            [CustomerController::class, 'orderDetail'])->name('order.detail');
    });

// ══════════════════════════════════════════════════════════════════════════════
// ORDER LIVE CHAT  (Customer, Driver, Admin)
// ══════════════════════════════════════════════════════════════════════════════

Route::middleware('auth')
    ->prefix('orders/{order}/chat')
    ->name('orders.chat.')
    ->group(function () {
        Route::get('/messages',  [OrderChatController::class, 'index'])->name('messages');
        Route::post('/messages', [OrderChatController::class, 'store'])->name('send');
    });
