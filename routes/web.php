<?php

use App\Http\Controllers\Admin\CashierController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KitchenController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\TableController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\MidtransController;
use App\Models\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Halaman utama: redirect sesuai status login
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('login');
});

// Otentikasi
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Rute Admin (Diproteksi: Wajib Login & Role Admin)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Manajemen Kategori
    Route::resource('categories', CategoryController::class)->except(['create', 'show', 'edit']);

    // Manajemen Menu & Varian
    Route::resource('menus', MenuController::class)->except(['show']);

    // Manajemen Meja & QR Code
    Route::resource('tables', TableController::class)->except(['create', 'show', 'edit']);
    Route::get('tables/{table}/print', [TableController::class, 'print'])->name('tables.print');
    Route::get('tables/{table}/download-qr', [TableController::class, 'downloadQr'])->name('tables.download_qr');

    // Modul Operasional Internal
    Route::get('cashier', [CashierController::class, 'index'])->name('cashier.index');
    Route::patch('cashier/{order}/status', [CashierController::class, 'updateStatus'])->name('cashier.status');

    Route::get('kitchen', [KitchenController::class, 'index'])->name('kitchen.index');
    Route::patch('kitchen/{order}/status', [KitchenController::class, 'updateStatus'])->name('kitchen.status');

    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
});

// Endpoint pemesanan mandiri via scan QR Code meja (Mendukung /order/{token} dan /table/{token})
Route::get('/order/{token}', [CustomerOrderController::class, 'index'])->name('order.show');
Route::get('/table/{token}', [CustomerOrderController::class, 'index'])->name('table.show');
Route::post('/order/{token}/checkout', [CustomerOrderController::class, 'checkout'])->name('order.checkout');
Route::get('/order/{token}/success/{order}', [CustomerOrderController::class, 'success'])->name('order.success');
Route::get('/order/{token}/check-status/{order}', [CustomerOrderController::class, 'checkStatus'])->name('order.check_status');

// Webhook Midtrans (juga tersedia di /api/midtrans/callback)
Route::post('/midtrans/callback', [MidtransController::class, 'callback']);
Route::post('/api/midtrans/callback', [MidtransController::class, 'callback']);
