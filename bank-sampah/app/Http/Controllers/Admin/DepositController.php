<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\WasteType;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DepositController extends Controller
{
    // 1. Tampilkan Form Setor Sampah
    public function create()
    {
        // Ambil data Nasabah saja dengan saldo wallet-nya
        $nasabahs = User::where('role', 'nasabah')
            ->with('wallet')
            ->orderBy('name')
            ->get();

        // Ambil Jenis Sampah untuk dropdown
        $wasteTypes = WasteType::all();

        return view('admin.deposits.create', compact('nasabahs', 'wasteTypes'));
    }

    // 2. Proses Simpan Transaksi
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'items' => 'required|array|min:1',
            'items.*.waste_type_id' => 'required|exists:waste_types,id',
            'items.*.weight' => 'required|numeric|min:0.1',
        ]);

        try {
            DB::beginTransaction();

            // A. Hitung Total
            $totalWeight = 0;
            $totalAmount = 0;

            // B. Buat Header Transaksi (no type/status/totals stored)
            $transaction = Transaction::create([
                'user_id' => $request->user_id,
                'staff_id' => Auth::id(), // Petugas yang login
                'date' => now()->toDateString(),
            ]);

            // C. Loop Item Sampah
            foreach ($request->items as $item) {
                $wasteType = WasteType::find($item['waste_type_id']);
                $subtotal = $wasteType->price_per_kg * $item['weight'];

                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'waste_type_id' => $wasteType->id,
                    'weight' => $item['weight'],
                    'subtotal' => $subtotal,
                ]);

                $totalWeight += $item['weight'];
                $totalAmount += $subtotal;
            }

            // D. Update Saldo Nasabah (Wallet)
            $wallet = Wallet::firstOrCreate(
                ['user_id' => $request->user_id],
                ['balance' => 0]
            );
            $wallet->increment('balance', $totalAmount);

            DB::commit();

            return redirect()->route('admin.deposits.create')
                ->with('success', 'Transaksi berhasil! Saldo nasabah bertambah Rp ' . number_format($totalAmount));

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
