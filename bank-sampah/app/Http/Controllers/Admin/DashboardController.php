<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Withdrawal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Cache stats for 60 seconds to improve performance
        $stats = Cache::remember('admin_dashboard_stats', 60, fn () => [
            'totalWeight' => (float) TransactionDetail::query()->sum('weight'),
            'totalBalance' => (float) Wallet::query()->sum('balance'),
            'totalWithdrawal' => (float) Withdrawal::query()->where('status', 'SUCCESS')->sum('amount'),
            'activeNasabahCount' => User::query()->where('role', 'NASABAH')->count(),
        ]);

        // Aktivitas Terakhir (include details for quick derived totals)
        // Optimized with specific columns and latest()
        $latestActivities = Transaction::query()
            ->with(['nasabah:id,name', 'details:id,transaction_id,weight,subtotal'])
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard', array_merge($stats, [
            'latestActivities' => $latestActivities,
        ]));
    }

    public function getChartData(Request $request)
    {
        $type = $request->query('type', 'mingguan');
        $offset = (int) $request->query('offset', 0);

        // Cache chart data for 5 minutes
        $cacheKey = "admin_dashboard_chart_{$type}_{$offset}";

        $chartData = Cache::remember($cacheKey, 300, function () use ($type, $offset) {
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
                        'transactions.date',
                        DB::raw('SUM(weight) as total_weight'),
                        DB::raw('SUM(subtotal) as total_amount')
                    )
                    ->groupBy('transactions.date')
                    ->get()
                    ->keyBy('date');

                for ($i = 0; $i < 7; $i++) {
                    $dateObj = $startDate->copy()->addDays($i);
                    $dateString = $dateObj->format('Y-m-d');
                    $stat = $stats->get($dateString);
                    $data->push([
                        'label' => $dateObj->translatedFormat('D').' ('.$dateObj->format('d/m').')',
                        'weight' => (float) ($stat->total_weight ?? 0),
                        'amount' => (float) ($stat->total_amount ?? 0),
                    ]);
                }

                $startMonth = $startDate->translatedFormat('F Y');
                $endMonth = $endDate->translatedFormat('F Y');
                $title = ($startMonth === $endMonth) ? $startMonth : "$startMonth - $endMonth";
            } else {
                $targetMonth = Carbon::today()->addMonths($offset);
                $startDate = $targetMonth->copy()->startOfMonth();
                $endDate = $targetMonth->copy()->endOfMonth();

                $stats = DB::table('transaction_details')
                    ->join('transactions', 'transactions.id', '=', 'transaction_details.transaction_id')
                    ->whereBetween('transactions.date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                    ->select(
                        'transactions.date',
                        DB::raw('SUM(weight) as total_weight'),
                        DB::raw('SUM(subtotal) as total_amount')
                    )
                    ->groupBy('transactions.date')
                    ->get()
                    ->keyBy('date');

                $daysInMonth = $startDate->daysInMonth;
                for ($i = 0; $i < $daysInMonth; $i++) {
                    $dateObj = $startDate->copy()->addDays($i);
                    $dateString = $dateObj->format('Y-m-d');
                    $stat = $stats->get($dateString);
                    $data->push([
                        'label' => $dateObj->format('d'),
                        'weight' => (float) ($stat->total_weight ?? 0),
                        'amount' => (float) ($stat->total_amount ?? 0),
                    ]);
                }

                $title = $targetMonth->translatedFormat('F Y');
            }

            return [
                'labels' => $data->pluck('label'),
                'weight' => $data->pluck('weight'),
                'amount' => $data->pluck('amount'),
                'title' => $title,
            ];
        });

        return response()->json($chartData);
    }
}
