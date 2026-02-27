<?php

namespace App\Http\Controllers\Nasabah;

use App\Http\Controllers\Controller;
use App\Http\Requests\Nasabah\UpdateBillingRequest;
use App\Http\Requests\Nasabah\WithdrawRequest;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WithdrawController extends Controller
{
    /**
     * Menampilkan riwayat penarikan saldo nasabah (Limit 10)
     */
    public function index()
    {
        // Optimasi: Gunakan Eager Loading pada user-wallet dan batasi kolom pada penarikan saldo
        $user = Auth::user();
        $wallet = $user->wallet;

        $withdrawals = Withdrawal::query()
            ->select(['id', 'date', 'amount', 'method', 'status', 'created_at'])
            ->where('user_id', $user->id)
            ->latest()
            ->limit(10)
            ->get();

        return view('nasabah.withdraw.index', compact('user', 'wallet', 'withdrawals'));
    }

    /**
     * Menyimpan permintaan penarikan saldo baru
     */
    public function store(WithdrawRequest $request)
    {
        $userId = Auth::id();
        $amount = (float) $request->validated('amount');

        return DB::transaction(function () use ($userId, $amount, $request) {
            // Gunakan lockForUpdate pada penarikan saldo atau cek saldo terakhir secara akurat
            // Namun karena wallet satu per user, ambil wallet saja
            $wallet = DB::table('wallets')->where('user_id', $userId)->lockForUpdate()->first();

            if (! $wallet || $amount > $wallet->balance) {
                return back()->withErrors(['amount' => 'Saldo tidak mencukupi'])->withInput();
            }

            // Simpan riwayat penarikan dengan status PENDING
            Withdrawal::create([
                'user_id' => $userId,
                'date' => now(),
                'amount' => $amount,
                'status' => 'PENDING',
                'method' => $request->validated('method'),
            ]);

            return redirect()->route('nasabah.withdraw.index')->with('success', 'Permintaan penarikan berhasil diajukan.');
        });
    }

    /**
     * Memperbarui informasi rekening bank nasabah
     */
    public function updateBilling(UpdateBillingRequest $request)
    {
        $user = Auth::user();
        $user->update($request->validated());

        return redirect()->route('nasabah.withdraw.index')->with('success', 'Informasi rekening berhasil disimpan.');
    }
}
