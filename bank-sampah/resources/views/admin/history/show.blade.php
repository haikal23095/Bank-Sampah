@extends('layouts.app')

@section('title', 'Detail Transaksi #' . $transaction->id)

@section('content')
<div class="max-w-3xl mx-auto">
    
    <a href="{{ route('admin.history.index') }}" class="flex items-center gap-2 text-gray-500 hover:text-gray-700 mb-6 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Kembali ke Riwayat
    </a>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        
        <div class="bg-gray-50 px-4 sm:px-8 py-6 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start gap-4">
            <div>
                <h2 class="text-lg font-bold text-gray-800">Transaksi #{{ $transaction->id }}</h2>
                <p class="text-sm text-gray-500">{{ $transaction->created_at->format('d F Y, H:i') }} WIB</p>
                <div class="mt-2">
                    @if($transaction->details->isNotEmpty())
                        <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full uppercase">Setor Sampah</span>
                    @else
                        <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-bold rounded-full uppercase">Penarikan Saldo</span>
                    @endif
                </div>
            </div>
            <div class="sm:text-right">
                <p class="text-xs text-gray-500 uppercase tracking-wide">Nasabah</p>
                <p class="font-bold text-gray-800 text-lg leading-tight">{{ $transaction->nasabah->name }}</p>
                <p class="text-sm text-gray-500">{{ $transaction->nasabah->email }}</p>
            </div>
        </div>

        @if($transaction->details->isNotEmpty())
        <div class="px-4 sm:px-8 py-6">
            <h3 class="text-sm font-bold text-gray-700 mb-4 uppercase tracking-wider">Rincian Sampah</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[400px]">
                    <thead>
                        <tr class="text-xs text-gray-400 border-b border-gray-100">
                            <th class="py-2">Jenis Sampah</th>
                            <th class="py-2">Harga / unit</th>
                            <th class="py-2">Berat</th>
                            <th class="py-2 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-700">
                        @foreach($transaction->details as $detail)
                        <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                            <td class="py-3 font-semibold">{{ $detail->wasteType->name ?? '-' }}</td>
                            <td class="py-3">Rp {{ number_format($detail->wasteType->price_per_kg ?? 0, 0, ',', '.') }}</td>
                            <td class="py-3">{{ number_format($detail->weight, 2) }} {{ $detail->wasteType->unit ?? 'kg' }}</td>
                            <td class="py-3 text-right font-bold text-emerald-600">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex flex-col sm:flex-row justify-between items-start sm:items-end gap-6 pt-6 border-t border-gray-100">
                <div class="bg-gray-50 p-3 rounded-lg border border-gray-100 w-full sm:w-auto">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Total Penimbangan</p>
                    <p class="text-lg font-bold text-gray-800">{{ number_format($transaction->total_weight, 2) }} kg</p>
                </div>
                <div class="sm:text-right w-full sm:w-auto">
                    <p class="text-xs text-gray-500 mb-1 font-semibold uppercase tracking-wider">Total Nominal</p>
                    <p class="text-3xl font-bold text-emerald-600">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        @else
        <!-- Withdrawal Details -->
        <div class="px-4 sm:px-8 py-10 text-center">
            <div class="w-20 h-20 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <p class="text-gray-500 font-medium mb-1 uppercase tracking-widest text-xs">Total Penarikan</p>
            <h3 class="text-4xl font-bold text-blue-600">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</h3>
        </div>
        @endif

        <div class="bg-gray-100 px-4 sm:px-8 py-5 border-t border-gray-200 flex flex-col sm:flex-row justify-between items-center gap-2">
            <div class="flex items-center gap-2 text-xs text-gray-500 font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                Petugas: <span class="text-gray-700 font-bold uppercase">{{ $transaction->petugas->name ?? 'Sistem' }}</span>
            </div>
            <div id="printButtonContainer">
                <button onclick="window.print()" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 flex items-center gap-1.5 transition uppercase tracking-wider">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak Struk
                </button>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body * { visibility: hidden; background: white !important; }
    .max-w-3xl, .max-w-3xl * { visibility: visible; }
    .max-w-3xl { position: absolute; left: 0; top: 0; width: 100%; border: none !important; shadow: none !important; }
    a[href="{{ route('admin.history.index') }}"], #printButtonContainer { display: none !important; }
}
</style>
    </div>
</div>
@endsection