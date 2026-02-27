<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\WithdrawalRequest;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends Controller
{
    /**
     * Display a listing of withdrawals.
     */
    public function index(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Optimized main query with specific columns
        $withdrawals = Withdrawal::query()
            ->with(['nasabah:id,name'])
            ->whereIn('status', ['SUCCESS', 'FAILED'])
            ->when($startDate, fn ($q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->whereDate('created_at', '<=', $endDate))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Pending withdrawals - limited columns
        $pendingWithdrawals = Withdrawal::query()
            ->with(['nasabah:id,name'])
            ->where('status', 'PENDING')
            ->latest()
            ->get(['id', 'user_id', 'amount', 'method', 'created_at']);

        // Nasabah list for dropdown - only needed columns
        $nasabahs = User::query()
            ->where('role', 'NASABAH')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.withdrawals.index', compact('withdrawals', 'nasabahs', 'pendingWithdrawals'));
    }

    public function store(WithdrawalRequest $request)
    {
        $userId = $request->user_id;
        $amount = (float) $request->amount;
        $method = $request->method;

        try {
            DB::beginTransaction();

            // Lock wallet to prevent race conditions
            $wallet = Wallet::query()
                ->where('user_id', $userId)
                ->lockForUpdate()
                ->first();

            if (! $wallet || $wallet->balance < $amount) {
                DB::rollBack();

                return back()->with('error', 'Saldo nasabah tidak mencukupi! Saldo saat ini: Rp '.number_format($wallet->balance ?? 0));
            }

            // Prevent rapid double submissions
            $recentDuplicate = Withdrawal::query()
                ->where('user_id', $userId)
                ->where('staff_id', Auth::id())
                ->where('amount', $amount)
                ->where('method', $method)
                ->where('created_at', '>=', now()->subSeconds(5))
                ->exists();

            if ($recentDuplicate) {
                DB::rollBack();

                return back()->with('error', 'Permintaan duplikat terdeteksi — tunggu sejenak sebelum mencoba lagi.');
            }

            // Update Balance
            $wallet->decrement('balance', $amount);

            // Create Note
            $note = $method === 'TRANSFER'
                ? "Bank: {$request->bank_name} | Rek: {$request->account_number}"
                : null;

            // Log Withdrawal
            $withdrawal = Withdrawal::create([
                'user_id' => $userId,
                'staff_id' => Auth::id(),
                'date' => now(),
                'amount' => $amount,
                'status' => 'SUCCESS',
                'method' => $method,
                'admin_note' => $note,
            ]);

            DB::commit();

            return back()->with([
                'new_withdrawal' => $withdrawal->id,
                'success' => 'Penarikan berhasil tercatat.',
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            return back()->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    public function approve($id)
    {
        try {
            DB::beginTransaction();

            $withdrawal = Withdrawal::query()
                ->where('id', $id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($withdrawal->status !== 'PENDING') {
                DB::rollBack();

                return back()->with('error', 'Permintaan ini sudah diproses.');
            }

            $wallet = Wallet::query()
                ->where('user_id', $withdrawal->user_id)
                ->lockForUpdate()
                ->first();

            if (! $wallet || $wallet->balance < $withdrawal->amount) {
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

            return back()->with([
                'new_withdrawal' => $withdrawal->id,
                'success' => 'Permintaan penarikan berhasil disetujui.',
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            return back()->with('error', 'Gagal memproses persetujuan: '.$e->getMessage());
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
