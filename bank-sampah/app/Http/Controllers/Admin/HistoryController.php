<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    // 1. Menampilkan Daftar Riwayat
    public function index()
    {
        // Ambil transaksi terbaru, sertakan data nasabah agar tidak query berulang
        $transactions = Transaction::with('nasabah')
                        ->latest()
                        ->paginate(10); // 10 data per halaman

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
