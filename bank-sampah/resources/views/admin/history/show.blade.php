@extends('layouts.app')

@section('title', 'Detail Transaksi #' . $transaction->id)

@section('content')
<div class="max-w-3xl mx-auto">
    
    <a href="{{ route('admin.history.index') }}" class="flex items-center gap-2 text-gray-500 hover:text-gray-700 mb-6 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Kembali ke Riwayat
    </a>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        
        <div class="bg-gray-50 px-8 py-6 border-b border-gray-100 flex justify-between items-start">
            <div>
                <h2 class="text-lg font-bold text-gray-800">Transaksi #{{ $transaction->id }}</h2>
                <p class="text-sm text-gray-500">{{ $transaction->created_at->format('d F Y, H:i') }} WIB</p>
                <div class="mt-2">
                    @if($transaction->type == 'DEPOSIT')
                        <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">SETOR SAMPAH</span>
                    @else
                        <span class="px-3 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full">PENARIKAN SALDO</span>
                    @endif
                </div>
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-500 uppercase tracking-wide">Nasabah</p>
                <p class="font-bold text-gray-800 text-lg">{{ $transaction->nasabah->name }}</p>
                <p class="text-sm text-gray-500">{{ $transaction->nasabah->email }}</p>
            </div>
        </div>

        @if($transaction->type == 'DEPOSIT' && $transaction->details->count() > 0)
        <div class="px-8 py-6">
            <h3 class="text-sm font-bold text-gray-700 mb-4 uppercase tracking-wider">Rincian Sampah</h3>
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-xs text-gray-400 border-b border-gray-100">
                        <th class="py-2">Jenis Sampah</th>
                        <th class="py-2">Harga / kg</th>
                        <th class="py-2">Berat</th>
                        <th class="py-2 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-gray-700">
                    @foreach($transaction->details as $detail)
                    <tr class="border-b border-gray-50">
                        <td class="py-3 font-medium">{{ $detail->wasteType->name }}</td>
                        <td class="py-3">Rp {{ number_format($detail->wasteType->price_per_kg, 0, ',', '.') }}</td>
                        <td class="py-3">{{ $detail->weight }} kg</td>
                        <td class="py-3 text-right font-bold">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <div class="bg-gray-50 px-8 py-6 border-t border-gray-100 flex justify-between items-center">
            <div>
                <p class="text-xs text-gray-500">Petugas: <span class="font-medium text-gray-700">{{ $transaction->petugas->name ?? 'Sistem' }}</span></p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500 mb-1">Total Transaksi</p>
                <p class="text-3xl font-bold text-green-600">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection