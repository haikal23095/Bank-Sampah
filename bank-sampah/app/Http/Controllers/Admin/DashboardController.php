<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Total Sampah (kg) - Only DEPOSIT
        $totalWeight = Transaction::where('type', 'DEPOSIT')
            ->where('status', 'SUCCESS')
            ->sum('total_weight');

        // 2. Total Saldo Nasabah
        $totalBalance = Wallet::sum('balance');

        // 3. Total Penarikan (Rp)
        $totalWithdrawal = Transaction::where('type', 'WITHDRAWAL')
            ->where('status', 'SUCCESS')
            ->sum('total_amount');

        // 4. Nasabah Aktif
        $activeNasabahCount = User::where('role', 'NASABAH')->count();

        // 5. Statistik Setoran (7 Hari Terakhir)
        $last7Days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $weight = Transaction::where('type', 'DEPOSIT')
                ->where('status', 'SUCCESS')
                ->whereDate('date', $date)
                ->sum('total_weight');
            
            $last7Days->push([
                'day' => $date->translatedFormat('D'),
                'weight' => $weight
            ]);
        }

        // 6. Aktivitas Terakhir
        $latestActivities = Transaction::with('nasabah')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalWeight',
            'totalBalance',
            'totalWithdrawal',
            'activeNasabahCount',
            'last7Days',
            'latestActivities'
        ));
    }
}
