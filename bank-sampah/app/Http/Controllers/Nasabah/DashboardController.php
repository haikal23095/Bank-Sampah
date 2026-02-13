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
        
        // Ambil riwayat deposit terakhir (include details)
        $latestTransactions = $user->transactions()
            ->with('details')
            ->latest()
            ->take(5)
            ->get();

        // Hitung total berat yang pernah disetor (dari details)
        $totalWeight = \App\Models\TransactionDetail::whereHas('transaction', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->sum('weight');

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
