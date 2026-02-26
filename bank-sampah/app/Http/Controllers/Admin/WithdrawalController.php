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
    public function index(Request $request)
    {
        $query = Withdrawal::with('nasabah')
            ->whereIn('status', ['SUCCESS', 'FAILED']);

        // Filter Tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $withdrawals = $query->latest()->paginate(10)->withQueryString();

        // Ambil penarikan yang masih PENDING
        $pendingWithdrawals = Withdrawal::with('nasabah')
            ->where('status', 'PENDING')
            ->latest()
            ->get();

        // Ambil list nasabah untuk dropdown di modal manual
        $nasabahs = User::where('role', 'nasabah')->orderBy('name')->get();

        return view('admin.withdrawals.index', compact('withdrawals', 'nasabahs', 'pendingWithdrawals'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1000',
            'method' => 'required|in:CASH,TRANSFER',
            // bank/account may be absent when method is CASH — allow null but require when method=TRANSFER
            'bank_name' => 'nullable|required_if:method,TRANSFER|string|max:255',
            'account_number' => 'nullable|required_if:method,TRANSFER|string|max:50',
        ]);

        $user = User::find($request->user_id);

        try {
            DB::beginTransaction();

            // lock wallet row to prevent race conditions from concurrent submissions
            $wallet = \App\Models\Wallet::where('user_id', $user->id)->lockForUpdate()->first();

            // 1. Cek Saldo (inside transaction after row lock)
            if (!$wallet || $wallet->balance < $request->amount) {
                DB::rollBack();
                return back()->with('error', 'Saldo nasabah tidak mencukupi! Saldo saat ini: Rp ' . number_format($wallet->balance ?? 0));
            }

            $staffId = Auth::id() ?? 1; // Fallback untuk Stress Test

            // Prevent near-duplicate submissions (same staff, user, amount, method within 5 seconds)
            $recentDuplicate = Withdrawal::where('user_id', $user->id)
                ->where('staff_id', $staffId)
                ->where('amount', $request->amount)
                ->where('method', $request->method)
                ->where('created_at', '>=', now()->subSeconds(5))
                ->exists();

            if ($recentDuplicate) {
                DB::rollBack();
                return back()->with('error', 'Permintaan duplikat terdeteksi — tunggu beberapa detik sebelum mencoba lagi.');
            }

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
                'staff_id' => $staffId,
                'date' => now(),
                'amount' => $request->amount,
                'status' => 'SUCCESS',
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

    public function approve($id)
    {
        $withdrawal = Withdrawal::findOrFail($id);

        if ($withdrawal->status !== 'PENDING') {
            return back()->with('error', 'Permintaan ini sudah diproses.');
        }

        try {
            DB::beginTransaction();

            $wallet = Wallet::where('user_id', $withdrawal->user_id)->lockForUpdate()->first();

            if (!$wallet || $wallet->balance < $withdrawal->amount) {
                DB::rollBack();
                return back()->with('error', 'Saldo nasabah tidak mencukupi untuk menyetujui penarikan ini.');
            }

            $wallet->decrement('balance', $withdrawal->amount);

            $withdrawal->update([
                'status' => 'SUCCESS',
                'staff_id' => Auth::id(),
                'date' => now(),
            ]);

            DB::commit();
            return back()->with(['new_withdrawal' => $withdrawal->id, 'success' => 'Permintaan penarikan berhasil disetujui.']);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal memproses persetujuan: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'admin_note' => 'required|string|max:255',
        ]);

        $withdrawal = Withdrawal::findOrFail($id);

        if ($withdrawal->status !== 'PENDING') {
            return back()->with('error', 'Permintaan ini sudah diproses.');
        }

        $withdrawal->update([
            'status' => 'FAILED',
            'staff_id' => Auth::id(),
            'admin_note' => $request->admin_note,
        ]);

        return back()->with('success', 'Permintaan penarikan telah ditolak.');
    }
}
