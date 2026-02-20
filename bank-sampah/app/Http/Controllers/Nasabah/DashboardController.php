<?php

namespace App\Http\Controllers\Nasabah;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Withdrawal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Memuat relasi wallet secara eager
        $user->load('wallet');

        // Hitung total berat yang pernah disetor (sum subtotal di transaction_details)
        $totalWeight = $user->transactions()
            ->join('transaction_details', 'transactions.id', '=', 'transaction_details.transaction_id')
            ->sum('transaction_details.weight');

        // Hitung total transaksi
        $totalTransactions = $user->transactions()->count();

        $weekOffset = request()->get('week', 0);
        $yearOffset = request()->get('year', 0);

        $dailyData = $this->getDailyStatistics($user->id, $weekOffset);
        $monthlyData = $this->getMonthlyStatistics($user->id, $yearOffset);

        // Aktivitas Terakhir (Transactions + Withdrawals)
        $latestActivities = $this->getLatestActivities($user->id);

        return view('nasabah.dashboard', compact(
            'user',
            'totalWeight',
            'totalTransactions',
            'dailyData',
            'monthlyData',
            'weekOffset',
            'yearOffset',
            'latestActivities'
        ));
    }

    public function getChartData(Request $request)
    {
        $user = Auth::user();
        $type = $request->query('type', 'daily');
        $offset = (int) $request->query('offset', 0);

        $data = collect();
        $title = '';

        if ($type === 'daily') {
            // Window 7 hari, bergerak berdasarkan offset * 7 hari
            $endDate = Carbon::today()->addDays($offset * 7);
            $startDate = $endDate->copy()->subDays(6);

            $stats = DB::table('transaction_details')
                ->join('transactions', 'transactions.id', '=', 'transaction_details.transaction_id')
                ->where('transactions.user_id', $user->id)
                ->whereBetween('transactions.date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->select(
                    DB::raw('DATE(transactions.date) as date'),
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
                    'amount' => (float) ($stat->total_amount ?? 0)
                ]);
            }

            // Title: Nama Bulan
            $startMonth = $startDate->translatedFormat('F Y');
            $endMonth = $endDate->translatedFormat('F Y');
            $title = ($startMonth === $endMonth) ? $startMonth : "$startMonth - $endMonth";

        } else {
            // Tahunan: 12 bulan dalam 1 tahun
            $targetYear = Carbon::today()->year + $offset;
            
            $stats = DB::table('transaction_details')
                ->join('transactions', 'transactions.id', '=', 'transaction_details.transaction_id')
                ->where('transactions.user_id', $user->id)
                ->whereYear('transactions.date', $targetYear)
                ->select(
                    DB::raw('MONTH(transactions.date) as month'),
                    DB::raw('SUM(subtotal) as total_amount')
                )
                ->groupBy('month')
                ->get()
                ->keyBy('month');

            // 12 bulan
            for ($month = 1; $month <= 12; $month++) {
                $stat = $stats->get($month);
                $monthName = Carbon::create($targetYear, $month, 1)->translatedFormat('M');
                $data->push([
                    'label' => $monthName,
                    'amount' => (float) ($stat->total_amount ?? 0)
                ]);
            }

            $title = "Tahun $targetYear";
        }

        return response()->json([
            'labels' => $data->pluck('label'),
            'amount' => $data->pluck('amount'),
            'title' => $title
        ]);
    }

    private function getDailyStatistics(int $userId, int $weekOffset = 0): Collection
    {
        $startOfWeek = Carbon::today()->startOfWeek()->subWeeks($weekOffset);
        $statistics = collect();

        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);

            $subtotal = Transaction::where('user_id', $userId)
                ->whereDate('date', $date)
                ->with('details')
                ->get()
                ->flatMap(fn ($t) => $t->details)
                ->sum('subtotal');

            $statistics->push([
                'date_label' => $date->format('Y/m/d'),
                'date' => $date->format('Y-m-d'),
                'amount' => $subtotal,
            ]);
        }

        return $statistics;
    }

    private function getMonthlyStatistics(int $userId, int $yearOffset = 0): Collection
    {
        $year = Carbon::today()->year - $yearOffset;

        $statistics = collect();

        for ($month = 1; $month <= 12; $month++) {
            $startDate = Carbon::create($year, $month, 1)->startOfMonth();
            $endDate = Carbon::create($year, $month, 1)->endOfMonth();

            $subtotal = Transaction::where('user_id', $userId)
                ->whereBetween('date', [$startDate, $endDate])
                ->with('details')
                ->get()
                ->flatMap(fn ($t) => $t->details)
                ->sum('subtotal');

            $statistics->push([
                'month' => $startDate->translatedFormat('M'),
                'year' => $year,
                'amount' => $subtotal,
            ]);
        }

        return $statistics;
    }

    private function getLatestActivities(int $userId): Collection
    {
        // Ambil transactions DEPOSIT
        $transactions = Transaction::where('user_id', $userId)
            ->with('details')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(fn ($transaction) => [
                'title' => 'Menyetor sampah',
                'amount' => $transaction->details->sum('subtotal'),
                'created_at' => $transaction->created_at,
                'status' => '',
            ]);

        // Ambil withdrawals
        $withdrawals = Withdrawal::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(fn ($withdrawal) => [
                'title' => 'Menarik saldo',
                'amount' => $withdrawal->amount,
                'created_at' => $withdrawal->created_at,
                'status' => $withdrawal->status,
            ]);

        // Gabung dan sort by created_at descending
        return $transactions->merge($withdrawals)
            ->sortByDesc('created_at')
            ->take(5)
            ->values();
    }
}
