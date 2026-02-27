<?php

namespace App\Http\Controllers\Nasabah;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Withdrawal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $userId = $user->id;

        // Eager load wallet balance specifically
        $user->load(['wallet:id,user_id,balance']);

        // Use cache for expensive stats
        $cacheKey = "nasabah_dashboard_stats_{$userId}";
        $stats = Cache::remember($cacheKey, 60, function () use ($userId) {
            return [
                'totalWeight' => (float) DB::table('transaction_details')
                    ->join('transactions', 'transactions.id', '=', 'transaction_details.transaction_id')
                    ->where('transactions.user_id', $userId)
                    ->sum('transaction_details.weight'),
                'totalTransactions' => Transaction::where('user_id', $userId)->count(),
            ];
        });

        $weekOffset = (int) request()->input('week', 0);
        $yearOffset = (int) request()->input('year', 0);

        // Optimized statistics fetching (no N+1)
        $dailyData = $this->getDailyStatistics($userId, $weekOffset);
        $monthlyData = $this->getMonthlyStatistics($userId, $yearOffset);

        // Latest Activities optimized
        $latestActivities = $this->getLatestActivities($userId);

        return view('nasabah.dashboard', array_merge($stats, [
            'user' => $user,
            'dailyData' => $dailyData,
            'monthlyData' => $monthlyData,
            'weekOffset' => $weekOffset,
            'yearOffset' => $yearOffset,
            'latestActivities' => $latestActivities,
        ]));
    }

    public function getChartData(Request $request)
    {
        $userId = Auth::id();
        $type = $request->query('type', 'daily');
        $offset = (int) $request->query('offset', 0);

        $cacheKey = "nasabah_chart_{$userId}_{$type}_{$offset}";

        return Cache::remember($cacheKey, 300, function () use ($userId, $type, $offset) {
            $data = collect();
            $title = '';

            if ($type === 'daily') {
                $endDate = Carbon::today()->addDays($offset * 7);
                $startDate = $endDate->copy()->subDays(6);

                $stats = DB::table('transaction_details')
                    ->join('transactions', 'transactions.id', '=', 'transaction_details.transaction_id')
                    ->where('transactions.user_id', $userId)
                    ->whereBetween('transactions.date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                    ->select(
                        DB::raw('DATE(transactions.date) as date'),
                        DB::raw('SUM(subtotal) as total_amount')
                    )
                    ->groupBy('date')
                    ->get()
                    ->keyBy('date');

                for ($i = 0; $i < 7; $i++) {
                    $dateObj = $startDate->copy()->addDays($i);
                    $dateString = $dateObj->format('Y-m-d');
                    $stat = $stats->get($dateString);
                    $data->push([
                        'label' => $dateObj->translatedFormat('D').' ('.$dateObj->format('d/m').')',
                        'amount' => (float) ($stat->total_amount ?? 0),
                    ]);
                }

                $startMonth = $startDate->translatedFormat('F Y');
                $endMonth = $endDate->translatedFormat('F Y');
                $title = ($startMonth === $endMonth) ? $startMonth : "$startMonth - $endMonth";

            } else {
                $targetYear = Carbon::today()->year + $offset;

                $stats = DB::table('transaction_details')
                    ->join('transactions', 'transactions.id', '=', 'transaction_details.transaction_id')
                    ->where('transactions.user_id', $userId)
                    ->whereYear('transactions.date', $targetYear)
                    ->select(
                        DB::raw('MONTH(transactions.date) as month'),
                        DB::raw('SUM(subtotal) as total_amount')
                    )
                    ->groupBy('month')
                    ->get()
                    ->keyBy('month');

                for ($month = 1; $month <= 12; $month++) {
                    $stat = $stats->get($month);
                    $monthName = Carbon::create($targetYear, $month, 1)->translatedFormat('M');
                    $data->push([
                        'label' => $monthName,
                        'amount' => (float) ($stat->total_amount ?? 0),
                    ]);
                }

                $title = "Tahun $targetYear";
            }

            return response()->json([
                'labels' => $data->pluck('label'),
                'amount' => $data->pluck('amount'),
                'title' => $title,
            ]);
        });
    }

    private function getDailyStatistics(int $userId, int $weekOffset = 0): Collection
    {
        $startOfWeek = Carbon::today()->startOfWeek()->subWeeks($weekOffset);
        $endOfWeek = $startOfWeek->copy()->addDays(6);

        // Fetch all data for the week in one query
        $stats = DB::table('transaction_details')
            ->join('transactions', 'transactions.id', '=', 'transaction_details.transaction_id')
            ->where('transactions.user_id', $userId)
            ->whereBetween('transactions.date', [$startOfWeek->format('Y-m-d'), $endOfWeek->format('Y-m-d')])
            ->select(
                DB::raw('DATE(transactions.date) as date'),
                DB::raw('SUM(subtotal) as total_amount')
            )
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $statistics = collect();
        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);
            $dateString = $date->format('Y-m-d');
            $stat = $stats->get($dateString);

            $statistics->push([
                'date_label' => $date->format('Y/m/d'),
                'date' => $dateString,
                'amount' => (float) ($stat->total_amount ?? 0),
            ]);
        }

        return $statistics;
    }

    private function getMonthlyStatistics(int $userId, int $yearOffset = 0): Collection
    {
        $year = Carbon::today()->year - $yearOffset;

        // Fetch all data for the year in one query
        $stats = DB::table('transaction_details')
            ->join('transactions', 'transactions.id', '=', 'transaction_details.transaction_id')
            ->where('transactions.user_id', $userId)
            ->whereYear('transactions.date', $year)
            ->select(
                DB::raw('MONTH(transactions.date) as month'),
                DB::raw('SUM(subtotal) as total_amount')
            )
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        $statistics = collect();
        for ($month = 1; $month <= 12; $month++) {
            $monthObj = Carbon::create($year, $month, 1);
            $stat = $stats->get($month);

            $statistics->push([
                'month' => $monthObj->translatedFormat('M'),
                'year' => $year,
                'amount' => (float) ($stat->total_amount ?? 0),
            ]);
        }

        return $statistics;
    }

    private function getLatestActivities(int $userId): Collection
    {
        // Optimized: fetching fewer records and using specific columns
        $transactions = Transaction::query()
            ->where('user_id', $userId)
            ->withSum('details', 'subtotal')
            ->latest()
            ->limit(5)
            ->get(['id', 'user_id', 'created_at'])
            ->map(fn ($t) => [
                'title' => 'Menyetor sampah',
                'amount' => (float) $t->details_sum_subtotal,
                'created_at' => $t->created_at,
                'status' => '',
            ]);

        $withdrawals = Withdrawal::query()
            ->where('user_id', $userId)
            ->latest()
            ->limit(5)
            ->get(['id', 'user_id', 'amount', 'status', 'created_at'])
            ->map(fn ($w) => [
                'title' => 'Menarik saldo',
                'amount' => (float) $w->amount,
                'created_at' => $w->created_at,
                'status' => $w->status,
            ]);

        return $transactions->merge($withdrawals)
            ->sortByDesc('created_at')
            ->take(5)
            ->values();
    }
}
