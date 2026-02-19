@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Riwayat Transaksi</h1>
        <p class="text-gray-500 text-sm mt-1">Pantau semua aktivitas setor dan tarik saldo.</p>

        <!-- Filter Tanggal -->
        <form action="{{ route('admin.history.index') }}" method="GET" class="flex flex-wrap items-end gap-3 mt-4">
            <div class="flex flex-col w-full sm:w-auto">
                <label class="text-[10px] font-bold text-gray-400 uppercase ml-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-xs focus:ring-2 focus:ring-blue-500 outline-none text-gray-600">
            </div>
            <div class="flex flex-col w-full sm:w-auto">
                <label class="text-[10px] font-bold text-gray-400 uppercase ml-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-xs focus:ring-2 focus:ring-blue-500 outline-none text-gray-600">
            </div>
            <div class="flex items-center gap-2 mb-1.5">
                <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-600 p-1.5 rounded-lg transition" title="Filter">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                </button>
                @if(request('start_date') || request('end_date'))
                    <a href="{{ route('admin.history.index') }}" class="text-xs text-red-500 hover:underline">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[700px]">
                <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                    <th class="px-6 py-4 font-semibold text-center">No</th>
                    <th class="px-6 py-4 font-semibold">ID / Tanggal</th>
                    <th class="px-6 py-4 font-semibold">Nasabah</th>
                    <th class="px-6 py-4 font-semibold">Total</th>
                    <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($transactions as $index => $trx)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 text-center text-sm text-gray-500">
                        {{ $transactions->firstItem() + $index }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="font-bold text-gray-700">#{{ $trx->id }}</span>
                        <p class="text-xs text-gray-500">{{ $trx->created_at->format('d M Y, H:i') }}</p>
                    </td>
                    
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-xs font-bold">
                                {{ substr($trx->nasabah->name, 0, 1) }}
                            </div>
                            <span class="text-sm font-medium text-gray-700">{{ $trx->nasabah->name }}</span>
                        </div>
                    </td>

                    <td class="px-6 py-4 font-bold text-gray-800">
                        @if($trx->details->isNotEmpty())
                            {{ number_format($trx->total_amount, 0, ',', '.') }} <span class="text-xs text-gray-500">( {{ number_format($trx->total_weight, 2) }} kg )</span>
                        @else
                            Rp {{ number_format($trx->total_amount ?? 0, 0, ',', '.') }}
                        @endif
                    </td>

                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('admin.history.show', $trx->id) }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm flex items-center justify-end gap-1">
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
        </div>
        
        <div class="p-4 border-t border-gray-100">
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection