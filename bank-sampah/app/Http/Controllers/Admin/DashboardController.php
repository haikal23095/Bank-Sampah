<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Models\TransactionDetail;
use App\Models\Withdrawal;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Total Sampah (kg) - sum from transaction_details
        $totalWeight = TransactionDetail::sum('weight');

        // 2. Total Saldo Nasabah
        $totalBalance = Wallet::sum('balance');

        // 3. Total Penarikan (Rp) - from withdrawals table
        $totalWithdrawal = Withdrawal::where('status', 'SUCCESS')->sum('amount');

        // 4. Nasabah Aktif
        $activeNasabahCount = User::where('role', 'NASABAH')->count();

        // 5. Statistik Setoran (7 Hari Terakhir)
        $last7Days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);

            $weight = DB::table('transaction_details')
                ->join('transactions', 'transactions.id', '=', 'transaction_details.transaction_id')
                ->whereDate('transactions.date', $date)
                ->sum('transaction_details.weight');
            
            $last7Days->push([
                'day' => $date->translatedFormat('D'),
                'weight' => $weight
            ]);
        }

        // 6. Aktivitas Terakhir (include details for quick derived totals)
        $latestActivities = Transaction::with(['nasabah', 'details'])
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
