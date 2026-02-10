@extends('layouts.app')

@title('Dashboard Nasabah')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-800">Selamat Datang, {{ Auth::user()->name }}! 👋</h1>
        <p class="text-gray-500">Lihat ringkasan tabungan dan aktivitas sampahmu di sini.</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Saldo -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-4 mb-4">
                <div class="p-3 bg-green-100 text-green-600 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="font-medium text-gray-500">Saldo Saat Ini</h3>
            </div>
            <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($user->wallet->balance ?? 0, 0, ',', '.') }}</p>
        </div>

        <!-- Total Berat -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-4 mb-4">
                <div class="p-3 bg-blue-100 text-blue-600 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
                <h3 class="font-medium text-gray-500">Total Sampah</h3>
            </div>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($totalWeight, 1) }} kg</p>
        </div>

        <!-- Jumlah Transaksi -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-4 mb-4">
                <div class="p-3 bg-purple-100 text-purple-600 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012-2"></path></svg>
                </div>
                <h3 class="font-medium text-gray-500">Total Transaksi</h3>
            </div>
            <p class="text-2xl font-bold text-gray-800">{{ $totalTransactions }} Kali</p>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-50">
            <h3 class="font-bold text-gray-800">Riwayat Transaksi Terakhir</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-gray-400 text-sm uppercase">
                        <th class="px-6 py-4 font-medium">Tanggal</th>
                        <th class="px-6 py-4 font-medium">Jenis</th>
                        <th class="px-6 py-4 font-medium">Berat/Nominal</th>
                        <th class="px-6 py-4 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($latestTransactions as $tx)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $tx->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-sm">
                                <span class="px-2 py-1 rounded-lg text-xs {{ $tx->type == 'DEPOSIT' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $tx->type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-800">
                                {{ $tx->type == 'DEPOSIT' ? $tx->total_weight . ' kg' : 'Rp ' . number_format($tx->total_amount) }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs text-green-500 font-medium capitalize">{{ $tx->status }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-400">Belum ada transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
