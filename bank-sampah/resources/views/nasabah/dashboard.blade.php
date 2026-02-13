@extends('layouts.app')

@section('title', 'Dashboard Nasabah')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Selamat Datang, {{ Auth::user()->name }}! 👋</h1>
            <p class="text-gray-500">Lihat ringkasan tabungan dan aktivitas sampahmu di sini.</p>
        </div>
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

    <!-- Statistik Setoran + Aktivitas Terakhir -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Statistik Setoran (2/3 width) -->
        <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-bold text-gray-800">Statistik Setoran</h3>
                <div class="flex gap-2">
                    <button class="toggle-chart-btn px-4 py-2 rounded-md text-sm font-medium transition"
                            data-period="daily" 
                            onclick="toggleChart('daily')"
                            id="btn-daily">
                        <span class="inline-block px-3 py-1.5 bg-green-600 text-white rounded-md">Harian</span>
                    </button>
                    <button class="toggle-chart-btn px-4 py-2 rounded-md text-sm font-medium transition"
                            data-period="weekly" 
                            onclick="toggleChart('weekly')"
                            id="btn-weekly">
                        <span class="inline-block px-3 py-1.5 bg-gray-100 text-gray-600 hover:bg-gray-200 rounded-md">Mingguan</span>
                    </button>
                </div>
            </div>
            <div class="relative h-64 w-full">
                <canvas id="setoranChart"></canvas>
            </div>
        </div>

        <!-- Aktivitas Terakhir (1/3 width) -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-800 mb-6">Aktivitas Terakhir</h3>
            
            <div class="space-y-6">
                @forelse($latestActivities as $activity)
                    <div class="flex gap-4">
                        <div class="w-2 h-2 mt-2 rounded-full {{ $activity['type'] == 'DEPOSIT' ? 'bg-green-500' : 'bg-red-500' }}"></div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-800 font-medium">
                                {{ $activity['title'] }}
                                <span class="block text-green-600">Rp {{ number_format($activity['amount'], 0, ',', '.') }}</span>
                            </p>
                            <p class="text-xs text-gray-400 mt-1">{{ $activity['created_at']->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 text-center py-4">Belum ada aktivitas.</p>
                @endforelse

                <a href="{{ route('nasabah.history.index') }}" class="block text-center text-green-600 text-sm font-medium mt-4 hover:underline">Lihat Semua Riwayat →</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let setoranChart = null;
    let currentPeriod = 'daily';

    const dailyData = {
        labels: {!! json_encode($last7Days->pluck('day')) !!},
        datasets: [{
            label: 'Setoran (Rp)',
            data: {!! json_encode($last7Days->pluck('amount')) !!},
            borderColor: '#16a34a',
            backgroundColor: 'rgba(22, 163, 74, 0.1)',
            tension: 0.4,
            fill: true
        }]
    };

    const weeklyData = {
        labels: {!! json_encode($last4Weeks->pluck('week')) !!},
        datasets: [{
            label: 'Setoran (Rp)',
            data: {!! json_encode($last4Weeks->pluck('amount')) !!},
            borderColor: '#16a34a',
            backgroundColor: 'rgba(22, 163, 74, 0.1)',
            tension: 0.4,
            fill: true
        }]
    };

    const chartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { 
                beginAtZero: true, 
                grid: { borderDash: [2, 4] },
                ticks: {
                    callback: function(value) {
                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                    }
                }
            },
            x: { grid: { display: false } }
        }
    };

    function initChart() {
        const ctx = document.getElementById('setoranChart').getContext('2d');
        const data = currentPeriod === 'daily' ? dailyData : weeklyData;
        
        if (setoranChart) {
            setoranChart.destroy();
        }

        setoranChart = new Chart(ctx, {
            type: 'line',
            data: data,
            options: chartOptions
        });
    }

    function toggleChart(period) {
        currentPeriod = period;

        // Update button styles
        document.getElementById('btn-daily').innerHTML = 
            period === 'daily' 
                ? '<span class="inline-block px-3 py-1.5 bg-green-600 text-white rounded-md">Harian</span>'
                : '<span class="inline-block px-3 py-1.5 bg-gray-100 text-gray-600 hover:bg-gray-200 rounded-md">Harian</span>';
        
        document.getElementById('btn-weekly').innerHTML = 
            period === 'weekly' 
                ? '<span class="inline-block px-3 py-1.5 bg-green-600 text-white rounded-md">Mingguan</span>'
                : '<span class="inline-block px-3 py-1.5 bg-gray-100 text-gray-600 hover:bg-gray-200 rounded-md">Mingguan</span>';

        initChart();
    }

    // Initialize chart on page load
    document.addEventListener('DOMContentLoaded', initChart);
</script>
@endsection
