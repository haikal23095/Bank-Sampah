@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Riwayat Transaksi</h1>
        <p class="text-gray-500 text-sm mt-1">Pantau semua aktivitas setor dan tarik saldo Anda.</p>
    </div>

    <!-- Filter Tanggal -->
    <form action="{{ route('nasabah.history.index') }}" method="GET" class="flex items-center gap-3 mt-4 mb-4">
        <div class="flex flex-col">
            <label class="text-[10px] font-bold text-gray-400 uppercase ml-1">Dari Tanggal</label>
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="border border-gray-200 rounded-lg px-3 py-1.5 text-xs focus:ring-2 focus:ring-blue-500 outline-none text-gray-600">
        </div>
        <div class="flex flex-col">
            <label class="text-[10px] font-bold text-gray-400 uppercase ml-1">Sampai Tanggal</label>
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="border border-gray-200 rounded-lg px-3 py-1.5 text-xs focus:ring-2 focus:ring-blue-500 outline-none text-gray-600">
        </div>
        <div class="flex items-end h-full pt-4">
            <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-600 p-1.5 rounded-lg transition" title="Filter">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
            </button>
            @if(request('start_date') || request('end_date'))
                <a href="{{ route('nasabah.history.index') }}" class="ml-2 text-xs text-red-500 hover:underline mb-2">Reset</a>
            @endif
        </div>
    </form>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                    <th class="px-6 py-4 font-semibold text-center">No</th>
                    <th class="px-6 py-4 font-semibold">ID / Tanggal</th>
                    <th class="px-6 py-4 font-semibold">Tipe</th>
                    <th class="px-6 py-4 font-semibold">Total (Rp)</th>
                    <th class="px-6 py-4 font-semibold">Status</th>
                    <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($transactions as $index => $trx)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="text-center text-sm text-gray-500">{{ $transactions->firstItem() + $index }}</div>
                    </td>

                    <td class="px-6 py-4">
                        <span class="font-bold text-gray-700">#{{ $trx->id }}</span>
                        <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($trx->date)->format('d M Y, H:i') }}</p>
                    </td>

                    <td class="px-6 py-4">
                        @if($trx->type === 'SETOR')
                            <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-md">SETOR</span>
                        @else
                            <span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-md">TARIK</span>
                        @endif
                    </td>

                    <td class="px-6 py-4 font-bold text-gray-800">
                        Rp {{ number_format($trx->total, 0, ',', '.') }}
                    </td>

                    <td class="px-6 py-4">
                        @php
                            $status = $trx->status;
                        @endphp
                        @if ($trx->type === 'SETOR')
                            <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs font-bold rounded-full">SETOR</span>
                        @else
                            @if ($status === 'SUCCESS')
                                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">{{ $status }}</span>
                            @elseif ($status === 'PENDING')
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs font-bold rounded-full">{{ $status }}</span>
                            @elseif ($status === 'FAILED')
                                <span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full">{{ $status }}</span>
                            @endif
                        @endif
                    </td>

                    <td class="px-6 py-4 text-right">
                        @php
                            $model = $trx->model;
                        @endphp
                        <a href="{{ 
                            $model === 'transaction'
                                ? route('nasabah.history.transaction', $trx->id)
                                : route('nasabah.history.withdrawal', $trx->id)
                        }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm flex items-center justify-end gap-1">
                            Detail
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">Belum ada riwayat transaksi.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="p-4 border-t border-gray-100">
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection
