@extends('layouts.app')

@if(isset($model) && $model === 'withdrawal')
    @section('title', 'Detail Penarikan #' . $record->id)
@else
    @section('title', 'Detail Transaksi #' . $record->id)
@endif

@section('content')
<div class="max-w-3xl mx-auto">
    
    <a href="{{ route('nasabah.history.index') }}" class="flex items-center gap-2 text-gray-500 hover:text-gray-700 mb-6 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Kembali ke Riwayat
    </a>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

        @if(isset($model) && $model === 'withdrawal')
            {{-- HEADER: Modern Receipt Style --}}
            <div class="px-8 pt-10 pb-8 text-center border-b border-dashed border-gray-200">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-50 text-blue-600 mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h2 class="text-3xl font-black text-gray-900 leading-tight">Rp {{ number_format($record->amount, 0, ',', '.') }}</h2>
                @php
                    $statusTheme = match($record->status) {
                        'SUCCESS' => 'text-emerald-500',
                        'FAILED'  => 'text-red-500',
                        'PENDING' => 'text-amber-500',
                        default   => 'text-blue-500',
                    };
                @endphp
                <p class="text-sm font-bold {{ $statusTheme }} uppercase tracking-[0.2em] mt-1">Status {{ $record->status }}</p>
                <p class="text-xs text-gray-400 mt-4">{{ $record->created_at->format('d F Y • H:i') }} WIB</p>
            </div>

            {{-- DETAIL CONTENT --}}
            <div class="px-8 py-10">
                <div class="space-y-6">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-2 border-b border-gray-50 pb-4">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">ID Penarikan</span>
                        <span class="text-sm font-black text-gray-800">#WDN-{{ str_pad($record->id, 6, '0', STR_PAD_LEFT) }}</span>
                    </div>

                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-2 border-b border-gray-50 pb-4">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Metode Penarikan</span>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                            <span class="text-sm font-black text-gray-800">{{ $record->method }}</span>
                        </div>
                    </div>

                    

                    {{-- Dynamic Status Box --}}
                    @if($record->status === 'FAILED')
                        <div class="mt-8 p-5 bg-red-50 rounded-2xl border border-red-100">
                            <div class="flex items-center gap-3 mb-2 text-red-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span class="text-xs font-black uppercase tracking-widest">Alasan Penolakan</span>
                            </div>
                            <p class="text-sm font-medium text-red-800 leading-relaxed italic">"{{ $record->admin_note ?? 'Saldo tidak mencukupi atau data tidak valid' }}"</p>
                        </div>
                    @endif
                </div>

            </div>


        @else
            {{-- Transaction view (existing) --}}
            <div class="bg-gray-50 px-8 py-6 border-b border-gray-100">
                <d  iv>
                    <h2 class="text-lg font-bold text-gray-800">Transaksi #{{ $record->id }}</h2>
                    <p class="text-sm text-gray-500">{{ $record->created_at->format('d F Y, H:i') }} WIB</p>
                    <div class="mt-2">
                        <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">SETOR SAMPAH</span>
                    </div>
                </d>
            </div>

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
                        @foreach($record->details as $detail)
                        <tr class="border-b border-gray-50">
                            <td class="py-3 font-medium">{{ $detail->wasteType->name ?? '-' }}</td>
                            <td class="py-3">Rp {{ number_format(($detail->subtotal/$detail->weight) ?? 0, 0, ',', '.') }}</td>
                            <td class="py-3">{{ $detail->weight }} kg</td>
                            <td class="py-3 text-right font-bold">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="bg-gray-50 px-8 py-6 border-t border-gray-100 flex justify-between items-center">
                <div>
                    <div class="text-sm text-gray-600">Total Berat: <span class="font-medium text-gray-800">{{ number_format($record->details->sum('weight'), 2) }} kg</span></div>
                    <p class="text-xs text-gray-500">Petugas: <span class="font-medium text-gray-700">{{ $record->petugas->name ?? 'Sistem' }}</span></p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500 mb-1">Total Transaksi</p>
                    <p class="text-3xl font-bold text-green-600">Rp {{ number_format($record->total_amount, 0, ',', '.') }}</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
