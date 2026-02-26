<?php

use App\Http\Controllers\Admin\CatalogController as AdminCatalogController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DepositController;
use App\Http\Controllers\Admin\HistoryController;
use App\Http\Controllers\Admin\WithdrawalController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Nasabah\CatalogController as NasabahCatalogController;
use App\Http\Controllers\Nasabah\DashboardController as NasabahDashboardController;
use App\Http\Controllers\Nasabah\HistoryController as NasabahHistoryController;
use App\Http\Controllers\Nasabah\WithdrawController as NasabahWithdrawController;
use Illuminate\Support\Facades\Route;

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

// --- RUTE TEMPORARY UNTUK STRESS TEST (TANPA AUTH) ---
Route::post('/api-test/setor', [DepositController::class, 'store'])->name('api.stress.setor');
Route::post('/api-test/penarikan', [WithdrawalController::class, 'store'])->name('api.stress.penarikan');

// Halaman Dashboard (Perlu Login)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/dashboard/chart-data', [AdminDashboardController::class, 'getChartData'])->name('admin.dashboard.chart');

    // --- FITUR SETOR SAMPAH ---
    Route::get('/setor-sampah', [DepositController::class, 'create'])->name('admin.deposits.create');
    // Route::post('/setor-sampah', [DepositController::class, 'store'])->name('admin.deposits.store'); // Dipindah ke atas

    // --- FITUR KELOLA NASABAH ---
    Route::get('/nasabah', [CustomerController::class, 'index'])->name('admin.customers.index');
    Route::post('/nasabah', [CustomerController::class, 'store'])->name('admin.customers.store');
    Route::put('/nasabah/{id}', [CustomerController::class, 'update'])->name('admin.customers.update');
    Route::delete('/nasabah/{id}', [CustomerController::class, 'destroy'])->name('admin.customers.destroy');

    // --- FITUR RIWAYAT TRANSAKSI ---
    Route::get('/riwayat', [HistoryController::class, 'index'])->name('admin.history.index');
    Route::get('/riwayat/{id}', [HistoryController::class, 'show'])->name('admin.history.show');

    // --- FITUR PENARIKAN SALDO ---
    Route::get('/penarikan', [WithdrawalController::class, 'index'])->name('admin.withdrawals.index');
    // Route::post('/penarikan', [WithdrawalController::class, 'store'])->name('admin.withdrawals.store'); // Dipindah ke atas
    Route::post('/penarikan/{id}/approve', [WithdrawalController::class, 'approve'])->name('admin.withdrawals.approve');
    Route::post('/penarikan/{id}/reject', [WithdrawalController::class, 'reject'])->name('admin.withdrawals.reject');

    // Katalog
    Route::get('/katalog', [AdminCatalogController::class, 'index'])->name('admin.catalog.index');
    Route::post('/katalog/item', [AdminCatalogController::class, 'storeType'])->name('admin.catalog.storeType');
    Route::put('/katalog/item/{id}', [AdminCatalogController::class, 'updateType'])->name('admin.catalog.updateType');
    Route::post('/katalog/category', [AdminCatalogController::class, 'storeCategory'])->name('admin.catalog.storeCategory');
    Route::put('/katalog/category/{id}', [AdminCatalogController::class, 'updateCategory'])->name('admin.catalog.updateCategory');
    Route::delete('/katalog/category/{id}', [AdminCatalogController::class, 'destroyCategory'])->name('admin.catalog.destroyCategory');
    Route::delete('/katalog/item/{id}', [AdminCatalogController::class, 'destroyType'])->name('admin.catalog.destroyType');
});

// Grup Khusus NASABAH
// Middleware 'auth' memastikan login, 'role:nasabah' memastikan dia nasabah
Route::middleware(['auth'])->prefix('nasabah')->group(function () {
    Route::middleware('role:nasabah')->group(function () {
        // Dashboard routes
        Route::get('/', [NasabahDashboardController::class, 'index'])->name('nasabah.index');
        Route::get('/dashboard', [NasabahDashboardController::class, 'index'])->name('nasabah.dashboard');
        Route::get('/dashboard/chart', [NasabahDashboardController::class, 'getChartData'])->name('nasabah.dashboard.chart');

        // Catalog routes
        Route::get('/catalog', [NasabahCatalogController::class, 'index'])->name('nasabah.catalog.index');

        // Withdraw (Tarik Saldo)
        Route::get('/tarik-saldo', [NasabahWithdrawController::class, 'index'])->name('nasabah.withdraw.index');
        Route::post('/tarik-saldo', [NasabahWithdrawController::class, 'store'])->name('nasabah.withdraw.store');
        Route::post('/billing', [NasabahWithdrawController::class, 'updateBilling'])->name('nasabah.billing.update');

        // History routes
        Route::get('/riwayat', [NasabahHistoryController::class, 'index'])->name('nasabah.history.index');

        // Transaction Detail
        Route::get(
            '/riwayat/transaction/{id}',
            [NasabahHistoryController::class, 'showTransaction']
        )->name('nasabah.history.transaction');

        // Withdrawal Detail
        Route::get(
            '/riwayat/withdrawal/{id}',
            [NasabahHistoryController::class, 'showWithdrawal']
        )->name('nasabah.history.withdrawal');
    });

});
