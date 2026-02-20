<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Models\TransactionDetail;
use App\Models\Withdrawal;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
            'latestActivities'
        ));
    }

    public function getChartData(Request $request)
    {
        $type = $request->query('type', 'mingguan');
        $offset = (int) $request->query('offset', 0);

        $data = collect();
        $title = '';

        if ($type === 'mingguan') {
            // Window 7 hari, bergerak berdasarkan offset * 7 hari
            $endDate = Carbon::today()->addDays($offset * 7);
            $startDate = $endDate->copy()->subDays(6);

            $stats = DB::table('transaction_details')
                ->join('transactions', 'transactions.id', '=', 'transaction_details.transaction_id')
                ->whereBetween('transactions.date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->select(
                    DB::raw('DATE(transactions.date) as date'),
                    DB::raw('SUM(weight) as total_weight'),
                    DB::raw('SUM(subtotal) as total_amount')
                )
                ->groupBy('date')
                ->get()
                ->keyBy('date');

            for ($i = 0; $i < 7; $i++) {
                $date = $startDate->copy()->addDays($i)->format('Y-m-d');
                $stat = $stats->get($date);
                $carbonDate = Carbon::parse($date);
                $data->push([
                    'label' => $carbonDate->translatedFormat('D') . ' (' . $carbonDate->format('d/m') . ')',
                    'weight' => (float) ($stat->total_weight ?? 0),
                    'amount' => (float) ($stat->total_amount ?? 0)
                ]);
            }

            // Title: Nama Bulan (Jika beda bulan, tampilkan dua-duanya)
            $startMonth = $startDate->translatedFormat('F Y');
            $endMonth = $endDate->translatedFormat('F Y');
            $title = ($startMonth === $endMonth) ? $startMonth : "$startMonth - $endMonth";

        } else {
            // Bulanan: Berbasis kalender bulan
            $targetMonth = Carbon::today()->addMonths($offset);
            $startDate = $targetMonth->copy()->startOfMonth();
            $endDate = $targetMonth->copy()->endOfMonth();

            $stats = DB::table('transaction_details')
                ->join('transactions', 'transactions.id', '=', 'transaction_details.transaction_id')
                ->whereBetween('transactions.date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->select(
                    DB::raw('DATE(transactions.date) as date'),
                    DB::raw('SUM(weight) as total_weight'),
                    DB::raw('SUM(subtotal) as total_amount')
                )
                ->groupBy('date')
                ->get()
                ->keyBy('date');

            $daysInMonth = $startDate->daysInMonth;
            for ($i = 0; $i < $daysInMonth; $i++) {
                $date = $startDate->copy()->addDays($i)->format('Y-m-d');
                $stat = $stats->get($date);
                $data->push([
                    'label' => Carbon::parse($date)->format('d'),
                    'weight' => (float) ($stat->total_weight ?? 0),
                    'amount' => (float) ($stat->total_amount ?? 0)
                ]);
            }

            $title = $targetMonth->translatedFormat('F Y');
        }

        return response()->json([
            'labels' => $data->pluck('label'),
            'weight' => $data->pluck('weight'),
            'amount' => $data->pluck('amount'),
            'title' => $title
        ]);
    }
}
