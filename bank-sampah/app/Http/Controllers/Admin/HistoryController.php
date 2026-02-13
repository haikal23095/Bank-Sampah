<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    // 1. Menampilkan Daftar Riwayat
    public function index(Request $request)
    {
        // Ambil transaksi terbaru, sertakan data nasabah dan details agar tidak query berulang
        $query = Transaction::with(['nasabah', 'details']);

        // Filter Tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $transactions = $query->latest()
            ->paginate(10)
            ->withQueryString(); // 10 data per halaman

        return view('admin.history.index', compact('transactions'));
    }

    // 2. Menampilkan Detail Satu Transaksi
    public function show($id)
    {
        // Ambil transaksi beserta detail item sampahnya dan petugas yang melayani
        $transaction = Transaction::with(['details.wasteType', 'nasabah', 'petugas'])
            ->findOrFail($id);

        return view('admin.history.show', compact('transaction'));
    }
}
