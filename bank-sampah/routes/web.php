<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Nasabah\DashboardController as NasabahDashboardController;
use App\Http\Controllers\Admin\DepositController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\HistoryController;
use App\Http\Controllers\Admin\WithdrawalController;
use App\Http\Controllers\Admin\CatalogController;


// Redirect root to login (use relative path to avoid absolute host:port generation)
Route::get('/', function () {
    return redirect('/login');
});

// Halaman Login (Tamu)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');



// Halaman Dashboard (Perlu Login)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    // --- FITUR SETOR SAMPAH ---
    Route::get('/setor-sampah', [DepositController::class, 'create'])->name('admin.deposits.create');
    Route::post('/setor-sampah', [DepositController::class, 'store'])->name('admin.deposits.store');

    // --- FITUR KELOLA NASABAH ---
    Route::get('/nasabah', [CustomerController::class, 'index'])->name('admin.customers.index');
    Route::post('/nasabah', [CustomerController::class, 'store'])->name('admin.customers.store');
    Route::put('/nasabah/{id}', [CustomerController::class, 'update'])->name('admin.customers.update');
    Route::delete('/nasabah/{id}', [CustomerController::class, 'destroy'])->name('admin.customers.destroy');

    // --- FITUR RIWAYAT TRANSAKSI ---
    Route::get('/riwayat', [HistoryController::class, 'index'])->name('admin.history.index');
    Route::get('/riwayat/{id}', [HistoryController::class, 'show'])->name('admin.history.show');

    // --- FITUR KATALOG SAMPAH ---
    Route::get('/katalog', [CatalogController::class, 'index'])->name('admin.catalog.index');
    Route::post('/katalog/kategori', [CatalogController::class, 'storeCategory'])->name('admin.catalog.storeCategory');
    Route::post('/katalog/item', [CatalogController::class, 'storeType'])->name('admin.catalog.storeType');
    Route::delete('/katalog/item/{id}', [CatalogController::class, 'destroyType'])->name('admin.catalog.destroyType');

    // --- FITUR PENARIKAN SALDO ---
    Route::get('/penarikan', [WithdrawalController::class, 'index'])->name('admin.withdrawals.index');
    Route::post('/penarikan', [WithdrawalController::class, 'store'])->name('admin.withdrawals.store');
    Route::post('/penarikan/{id}/approve', [WithdrawalController::class, 'approve'])->name('admin.withdrawals.approve');
    Route::post('/penarikan/{id}/reject', [WithdrawalController::class, 'reject'])->name('admin.withdrawals.reject');

    // Katalog
    Route::get('/katalog', [CatalogController::class, 'index'])->name('admin.catalog.index');
    Route::post('/katalog/item', [CatalogController::class, 'storeType'])->name('admin.catalog.storeType');
    Route::delete('/katalog/item/{id}', [CatalogController::class, 'destroyType'])->name('admin.catalog.destroyType');
});

// 2. Grup Khusus NASABAH
// Middleware 'auth' memastikan login, 'role:nasabah' memastikan dia nasabah
Route::middleware(['auth', 'role:nasabah'])->prefix('nasabah')->group(function () {

    Route::get('/dashboard', [NasabahDashboardController::class, 'index'])->name('nasabah.dashboard');

    // Nanti tambahkan route lain di sini, misal:
    // Route::get('/riwayat', [NasabahController::class, 'history']);
});
