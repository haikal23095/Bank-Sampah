@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Halo, {{ Auth::user()->name }} 👋</h1>
            <p class="text-gray-500 mt-1">Selamat datang di dashboard EcoBank Anda.</p>
        </div>
        <button class="bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-lg flex items-center gap-2 shadow-md transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            Konsultasi Sampah AI
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-blue-50 rounded-lg text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path></svg>
                </div>
            </div>
            <p class="text-gray-500 text-sm mb-1">Total Sampah (kg)</p>
            <h3 class="text-2xl font-bold text-gray-800">{{ number_format($totalWeight, 2) }} kg</h3>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-green-50 rounded-lg text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </div>
            </div>
            <p class="text-gray-500 text-sm mb-1">Total Saldo Nasabah</p>
            <h3 class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalBalance, 0, ',', '.') }}</h3>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-red-50 rounded-lg text-red-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
                </div>
            </div>
            <p class="text-gray-500 text-sm mb-1">Total Penarikan</p>
            <h3 class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalWithdrawal, 0, ',', '.') }}</h3>
        </div>

         <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-purple-50 rounded-lg text-purple-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
            </div>
            <p class="text-gray-500 text-sm mb-1">Nasabah Aktif</p>
            <h3 class="text-2xl font-bold text-gray-800">{{ $activeNasabahCount }}</h3>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-bold text-gray-800">Statistik Setoran (7 Hari Terakhir)</h3>
                <select class="text-sm border-gray-300 rounded-md text-gray-600 bg-gray-50 p-1">
                    <option>Mingguan</option>
                    <option>Bulanan</option>
                </select>
            </div>
            <div class="relative h-64 w-full">
                <canvas id="setoranChart"></canvas>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-800 mb-6">Aktivitas Terakhir</h3>
            
            <div class="space-y-6">
                @forelse($latestActivities as $activity)
                    <div class="flex gap-4">
                        <div class="w-2 h-2 mt-2 rounded-full {{ $activity->details->isNotEmpty() ? 'bg-green-500' : 'bg-red-500' }}"></div>
                        <div>
                            <p class="text-sm text-gray-800 font-medium">
                                {{ $activity->nasabah->name }} 
                                {{ $activity->details->isNotEmpty() ? 'menyetor' : 'menarik' }} 
                                {{ $activity->details->isNotEmpty() ? $activity->total_weight . 'kg sampah' : 'Rp ' . number_format($activity->total_amount ?? 0, 0, ',', '.') }}
                            </p>
                            <p class="text-xs text-gray-400">{{ $activity->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 text-center py-4">Belum ada aktivitas.</p>
                @endforelse

                <a href="#" class="block text-center text-green-600 text-sm font-medium mt-4 hover:underline">Lihat Semua Riwayat</a>
            </div>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('setoranChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($last7Days->pluck('day')) !!},
                datasets: [{
                    label: 'Berat (kg)',
                    data: {!! json_encode($last7Days->pluck('weight')) !!},
                    borderColor: '#16a34a', // green-600
                    backgroundColor: 'rgba(22, 163, 74, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { borderDash: [2, 4] } },
                    x: { grid: { display: false } }
                }
            }
        });
    </script>
@endsection