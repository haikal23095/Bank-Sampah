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
            {{-- HEADER --}}
            <div class="bg-gray-50 px-8 py-6 border-b border-gray-100">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">
                        Penarikan #{{ $record->id }}
                    </h2>
                    <p class="text-sm text-gray-500">
                        {{ optional($record->date ?? $record->created_at)->format('d F Y, H:i') }} WIB
                    </p>

                    <div class="mt-2">
                        @php
                            $statusColor = match($record->status) {
                                'PENDING' => 'bg-yellow-100 text-yellow-700',
                                'FAILED' => 'bg-red-100 text-red-700',
                                'SUCCESS' => 'bg-green-100 text-green-700',
                                default => 'bg-gray-100 text-gray-700',
                            };
                        @endphp

                        <span class="px-3 py-1 text-xs font-bold rounded-full {{ $statusColor }}">
                            {{ $record->status }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- DETAIL --}}
            <div class="px-8 py-6">
                <h3 class="text-sm font-bold text-gray-700 mb-4 uppercase tracking-wider">
                    Detail Penarikan
                </h3>

                <div class="grid grid-cols-2 gap-y-4 text-sm text-gray-700">
                    <div>
                        <p class="text-gray-500">Metode</p>
                        <p class="font-medium text-gray-800">{{ $record->method }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Diproses Oleh</p>
                        <p class="font-medium text-gray-800">
                            {{ $record->staff_id ? 'Staff #' . $record->staff_id : 'Belum diproses' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-500">Tanggal Pengajuan</p>
                        <p class="font-medium text-gray-800">
                            {{ optional($record->date ?? $record->created_at)->format('d F Y, H:i') }}
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-500">Dibuat Pada</p>
                        <p class="font-medium text-gray-800">
                            {{ $record->created_at->format('d F Y, H:i') }}
                        </p>
                    </div>

                    <div class="col-span-2">
                        <p class="text-gray-500">Catatan Admin</p>
                        <p class="font-medium text-gray-800">
                            {{ $record->admin_note ?? '-' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- SUMMARY --}}
            <div class="bg-gray-50 px-8 py-6 border-t border-gray-100 flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Total Penarikan</p>
                    <p class="text-3xl font-bold text-blue-600">
                        Rp {{ number_format($record->amount, 0, ',', '.') }}
                    </p>
                </div>

                <div class="text-right text-sm text-gray-600">
                    <p>Status: 
                        <span class="font-semibold">{{ $record->status }}</span>
                    </p>
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
