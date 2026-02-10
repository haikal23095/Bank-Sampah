<?php

namespace App\Http\Controllers\Nasabah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Memuat relasi wallet secara eager
        $user->load('wallet');
        
        // Ambil riwayat deposit terakhir
        $latestTransactions = $user->transactions()
            ->latest()
            ->take(5)
            ->get();

        // Hitung total berat yang pernah disetor
        $totalWeight = $user->transactions()
            ->where('type', 'DEPOSIT')
            ->where('status', 'SUCCESS')
            ->sum('total_weight');

        // Hitung total transaksi
        $totalTransactions = $user->transactions()->count();

        return view('nasabah.dashboard', compact(
            'user', 
            'latestTransactions', 
            'totalWeight', 
            'totalTransactions'
        ));
    }
}
