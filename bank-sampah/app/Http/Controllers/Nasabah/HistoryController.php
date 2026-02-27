<?php

namespace App\Http\Controllers\Nasabah;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{
    /**
     * Menampilkan daftar riwayat transaksi dan penarikan nasabah
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        // Date filters
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Optimasi: Gunakan withSum untuk menghindari loading semua detail ke memori
        $txQuery = Transaction::query()
            ->select(['id', 'date', 'created_at'])
            ->where('user_id', $userId)
            ->withSum('details', 'subtotal');

        if ($startDate) {
            $txQuery->whereDate('date', '>=', $startDate);
        }
        if ($endDate) {
            $txQuery->whereDate('date', '<=', $endDate);
        }

        $transactionsData = $txQuery->get()->map(function ($t) {
            return (object) [
                'id' => $t->id,
                'date' => $t->date ?? $t->created_at,
                'type' => 'SETOR',
                'total' => (float) $t->details_sum_subtotal,
                'status' => 'SUCCESS',
                'model' => 'transaction',
            ];
        });

        // Optimasi: Ambil hanya kolom yang diperlukan
        $wdQuery = Withdrawal::query()
            ->select(['id', 'date', 'amount', 'status', 'created_at'])
            ->where('user_id', $userId);

        if ($startDate) {
            $wdQuery->whereDate('date', '>=', $startDate);
        }
        if ($endDate) {
            $wdQuery->whereDate('date', '<=', $endDate);
        }

        $withdrawalsData = $wdQuery->get()->map(function ($w) {
            return (object) [
                'id' => $w->id,
                'date' => $w->date ?? $w->created_at,
                'type' => 'TARIK',
                'total' => (float) $w->amount,
                'status' => $w->status,
                'model' => 'withdrawal',
            ];
        });

        // Gabungkan dan urutkan
        $merged = $transactionsData->concat($withdrawalsData)
            ->sortByDesc(fn ($item) => is_string($item->date) ? $item->date : $item->date->toDateTimeString())
            ->values();

        // Pagination Manual yang efisien
        $page = (int) $request->input('page', 1);
        $perPage = 10;
        $total = $merged->count();
        $items = $merged->slice(($page - 1) * $perPage, $perPage)->values();

        $transactions = new LengthAwarePaginator($items, $total, $perPage, $page, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);

        return view('nasabah.history.index', compact('transactions'));
    }

    /**
     * Menampilkan detail satu transaksi setor
     */
    public function showTransaction($id)
    {
        $transaction = Transaction::query()
            ->where('user_id', Auth::id())
            ->with(['details.wasteType:id,name,unit', 'petugas:id,name'])
            ->findOrFail($id);

        return view('nasabah.history.show', [
            'model' => 'transaction',
            'record' => $transaction,
        ]);
    }

    /**
     * Menampilkan detail satu penarikan saldo
     */
    public function showWithdrawal($id)
    {
        $withdrawal = Withdrawal::query()
            ->where('user_id', Auth::id())
            ->with('petugas:id,name')
            ->findOrFail($id);

        return view('nasabah.history.show', [
            'model' => 'withdrawal',
            'record' => $withdrawal,
        ]);
    }
}
