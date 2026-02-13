<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Withdrawal;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class WithdrawalController extends Controller
{
    public function index()
    {
        // Ambil data penarikan dari tabel withdrawals, urutkan terbaru
        $withdrawals = Withdrawal::with('nasabah')
                        ->latest()
                        ->paginate(10);
        
        // Ambil list nasabah untuk dropdown di modal
        $nasabahs = User::where('role', 'nasabah')->orderBy('name')->get();

        return view('admin.withdrawals.index', compact('withdrawals', 'nasabahs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1000',
            'method' => 'required|in:CASH,TRANSFER',
            'bank_name' => 'required_if:method,TRANSFER|string|max:255',
            'account_number' => 'required_if:method,TRANSFER|string|max:50',
        ]);

        $user = User::find($request->user_id);
        $wallet = $user->wallet; // Asumsi relasi hasOne Wallet ada di User

        // 1. Cek Saldo
        if (!$wallet || $wallet->balance < $request->amount) {
            return back()->with('error', 'Saldo nasabah tidak mencukupi! Saldo saat ini: Rp ' . number_format($wallet->balance ?? 0));
        }

        try {
            DB::beginTransaction();

            // 2. Kurangi Saldo
            $wallet->decrement('balance', $request->amount);

            // 3. Catat Note (Simpan Info Bank jika Transfer)
            $note = null;
            if ($request->method === 'TRANSFER') {
                $note = "Bank: " . $request->bank_name . " | Rek: " . $request->account_number;
            }

            // 4. Catat Penarikan ke tabel withdrawals
            $withdrawal = Withdrawal::create([
                'user_id' => $user->id,
                'staff_id' => Auth::id(),
                'date' => now(),
                'amount' => $request->amount,
                'status' => 'SUCCESS', // Anggap langsung sukses krn manual admin
                'method' => $request->method,
                'admin_note' => $note
            ]);

            DB::commit();

            // 5. Redirect Balik dengan Data Penarikan Baru (untuk memicu Modal Nota)
            return back()->with(['new_withdrawal' => $withdrawal->id, 'success' => 'Penarikan berhasil tercatat.']);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
