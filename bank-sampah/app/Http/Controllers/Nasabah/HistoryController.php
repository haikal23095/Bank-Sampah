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
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Ambil transaksi (setor)
        $txQuery = Transaction::where('user_id', $userId)->with('details.wasteType');
        if ($startDate) {
            $txQuery->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $txQuery->whereDate('created_at', '<=', $endDate);
        }
        $transactions = $txQuery->get();

        // Map transaksi ke format standar
        $transRecords = $transactions->map(function ($t) {
            $total = $t->details->sum('subtotal');

            return (object) [
                'id' => $t->id,
                'date' => $t->created_at,
                'type' => 'SETOR',
                'total' => $total,
                'status' => 'SUCCESS',
                'model' => 'transaction',
            ];
        });

        // Ambil penarikan (tarik)
        $wdQuery = Withdrawal::where('user_id', $userId);
        if ($startDate) {
            $wdQuery->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $wdQuery->whereDate('created_at', '<=', $endDate);
        }
        $withdrawals = $wdQuery->get();

        $withdrawRecords = $withdrawals->map(function ($w) {
            return (object) [
                'id' => $w->id,
                'date' => $w->date ?? $w->created_at,
                'type' => 'TARIK',
                'total' => $w->amount,
                'status' => $w->status,
                'model' => 'withdrawal',
            ];
        });

        // Gabungkan, urutkan berdasarkan tanggal, dan paginate manual
        $merged = $transRecords->concat($withdrawRecords)->sortByDesc('date')->values();

        $page = (int) $request->get('page', 1);
        $perPage = 10;
        $total = $merged->count();

        $items = $merged->slice(($page - 1) * $perPage, $perPage)->values();

        $paginator = new LengthAwarePaginator($items, $total, $perPage, $page, [
            'path' => route('nasabah.history.index'),
            'query' => $request->query(),
        ]);

        // Beri nama $transactions agar view tidak banyak berubah
        $transactions = $paginator;

        return view('nasabah.history.index', compact('transactions'));
    }

    /**
     * Menampilkan detail satu transaksi
     */
    public function showTransaction($id)
    {
        $transaction = Transaction::where('user_id', Auth::id())
            ->with(['details.wasteType'])
            ->findOrFail($id);

        return view('nasabah.history.show', [
            'model' => 'transaction',
            'record' => $transaction,
        ]);
    }

    public function showWithdrawal($id)
    {
        $withdrawal = Withdrawal::where('user_id', Auth::id())
            ->findOrFail($id);

        return view('nasabah.history.show', [
            'model' => 'withdrawal',
            'record' => $withdrawal,
        ]);
    }
}
