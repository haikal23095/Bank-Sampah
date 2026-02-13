<?php

namespace App\Http\Controllers\Nasabah;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Withdrawal;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Memuat relasi wallet secara eager
        $user->load('wallet');

        // Hitung total berat yang pernah disetor
        $totalWeight = $user->transactions()
            ->where('type', 'DEPOSIT')
            ->sum('total_weight');

        // Hitung total transaksi
        $totalTransactions = $user->transactions()->count();

        // Statistik Setoran 7 Hari Terakhir
        $last7Days = $this->getLast7DaysStatistics($user->id);

        // Statistik Setoran 4 Minggu Terakhir
        $last4Weeks = $this->getLast4WeeksStatistics($user->id);

        // Aktivitas Terakhir (Transactions + Withdrawals)
        $latestActivities = $this->getLatestActivities($user->id);

        return view('nasabah.dashboard', compact(
            'user',
            'totalWeight',
            'totalTransactions',
            'last7Days',
            'last4Weeks',
            'latestActivities'
        ));
    }

    private function getLast7DaysStatistics(int $userId): Collection
    {
        $statistics = collect();

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $subtotal = Transaction::where('user_id', $userId)
                ->where('type', 'DEPOSIT')
                ->where('status', 'SUCCESS')
                ->whereDate('date', $date)
                ->with('details')
                ->get()
                ->flatMap(fn ($transaction) => $transaction->details)
                ->sum('subtotal');

            $statistics->push([
                'day' => $date->translatedFormat('D'),
                'date' => $date->format('Y-m-d'),
                'amount' => $subtotal,
            ]);
        }

        return $statistics;
    }

    private function getLast4WeeksStatistics(int $userId): Collection
    {
        $statistics = collect();

        for ($i = 3; $i >= 0; $i--) {
            $startDate = Carbon::today()->subWeeks($i)->startOfWeek();
            $endDate = Carbon::today()->subWeeks($i)->endOfWeek();

            $subtotal = Transaction::where('user_id', $userId)
                ->where('type', 'DEPOSIT')
                ->where('status', 'SUCCESS')
                ->whereBetween('date', [$startDate, $endDate])
                ->with('details')
                ->get()
                ->flatMap(fn ($transaction) => $transaction->details)
                ->sum('subtotal');

            $weekLabel = $startDate->translatedFormat('d M').' - '.$endDate->translatedFormat('d M');

            $statistics->push([
                'week' => $weekLabel,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'amount' => $subtotal,
            ]);
        }

        return $statistics;
    }

    private function getLatestActivities(int $userId): Collection
    {
        // Ambil transactions DEPOSIT
        $transactions = Transaction::where('user_id', $userId)
            ->where('type', 'DEPOSIT')
            ->with('details')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(fn ($transaction) => [
                'type' => 'DEPOSIT',
                'title' => 'Menyetor sampah',
                'amount' => $transaction->details->sum('subtotal'),
                'created_at' => $transaction->created_at,
            ]);

        // Ambil withdrawals
        $withdrawals = Withdrawal::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(fn ($withdrawal) => [
                'type' => 'WITHDRAWAL',
                'title' => 'Menarik saldo',
                'amount' => $withdrawal->amount,
                'created_at' => $withdrawal->created_at,
            ]);

        // Gabung dan sort by created_at descending
        return $transactions->merge($withdrawals)
            ->sortByDesc('created_at')
            ->take(5)
            ->values();
    }
}
