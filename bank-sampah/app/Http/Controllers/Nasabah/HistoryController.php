<?php

namespace App\Http\Controllers\Nasabah;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{
    /**
     * Menampilkan daftar riwayat transaksi dan penarikan nasabah
     */
    public function index()
    {
        $userId = Auth::id();

        // Ambil transaksi nasabah terbaru
        $transactions = Transaction::where('user_id', $userId)
            ->with('nasabah', 'details.wasteType')
            ->latest()
            ->paginate(10);

        return view('nasabah.history.index', compact('transactions'));
    }

    /**
     * Menampilkan detail satu transaksi
     */
    public function show($id)
    {
        $userId = Auth::id();

        // Ambil transaksi dan pastikan milik user yang sedang login
        $transaction = Transaction::where('user_id', $userId)
            ->with(['details.wasteType', 'nasabah', 'petugas'])
            ->findOrFail($id);

        return view('nasabah.history.show', compact('transaction'));
    }
}
