<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DepositController;
use App\Http\Controllers\Nasabah\DashboardController as NasabahDashboardController;


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
});

// Grup Khusus NASABAH
// Middleware 'auth' memastikan login, 'role:nasabah' memastikan dia nasabah
Route::middleware(['auth', 'role:nasabah'])->prefix('nasabah')->group(function () {
    Route::get('/', [NasabahDashboardController::class, 'index'])->name('nasabah.dashboard');

    Route::get('/dashboard', [NasabahDashboardController::class, 'index'])->name('nasabah.dashboard');

    // Nanti tambahkan route lain di sini, misal:
    // Route::get('/riwayat', [NasabahController::class, 'history']);
});
