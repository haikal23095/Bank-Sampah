<?php

namespace App\Http\Controllers\Nasabah;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WithdrawController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $wallet = $user->wallet;
        $withdrawals = Withdrawal::where('user_id', $user->id)->latest()->limit(10)->get();
        return view('nasabah.withdraw.index', compact('user', 'wallet', 'withdrawals'));
    }

    public function store(Request $request)
    {
        $messages = [
            'amount.required' => 'Jumlah Penarikan Minimal Rp. 10.000',
            'amount.numeric' => 'Jumlah Penarikan Minimal Rp. 10.000',
            'amount.min' => 'Jumlah Penarikan Minimal Rp. 10.000',
        ];

        $request->validate([
            'method' => 'required|in:CASH,TRANSFER',
            'amount' => 'required|numeric|min:10000',
        ], $messages);
            
        $user = Auth::user();

        // Check balance
        $balance = optional($user->wallet)->balance ?? 0;
        $amount = (float) $request->input('amount');

        if ($amount > $balance) {
            return back()->withErrors(['amount' => 'Saldo tidak mencukupi'])->withInput();
        }


        // Create withdrawal with status PENDING
        Withdrawal::create([
            'user_id' => $user->id,
            'date' => now(),
            'amount' => $amount,
            'status' => 'PENDING',
            'method' => $request->input('method'),
        ]);

        return redirect()->route('nasabah.withdraw.index')->with('success', 'Permintaan penarikan berhasil diajukan.');
    }

    public function updateBilling(Request $request)
    {
        $request->validate([
            'bank_name' => 'nullable|string|max:191',
            'account_number' => 'nullable|string|max:191',
        ]);

        $user = Auth::user();
        $user->bank_name = $request->input('bank_name');
        $user->account_number = $request->input('account_number');
        $user->save();

        return redirect()->route('nasabah.withdraw.index')->with('success', 'Informasi rekening berhasil disimpan.');
    }
}
