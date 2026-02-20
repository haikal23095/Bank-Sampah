@extends('layouts.app')

@section('title', 'Dashboard Nasabah')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8 animate-in fade-in slide-in-from-top duration-500">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Halo, {{ Auth::user()->name }} 👋</h1>
            <p class="text-gray-500 text-sm mt-2">Selamat datang di dashboard Bank Sampah Anda. Lihat ringkasan tabungan dan aktivitas sampahmu di sini.</p>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Saldo -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-300 animate-in fade-in slide-in-from-bottom duration-500" style="animation-delay: 0.1s;">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Saldo Saat Ini</p>
                </div>
                <div class="p-3 bg-emerald-50 rounded-lg text-emerald-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <h3 class="text-3xl font-bold text-gray-900">Rp {{ number_format($user->wallet->balance ?? 0, 0, ',', '.') }}</h3>
        </div>

        <!-- Total Berat -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-300 animate-in fade-in slide-in-from-bottom duration-500" style="animation-delay: 0.2s;">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Sampah Disetor</p>
                </div>
                <div class="p-3 bg-blue-50 rounded-lg text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
            </div>
            <h3 class="text-3xl font-bold text-gray-900">{{ number_format($totalWeight, 1) }} kg</h3>
        </div>

        <!-- Jumlah Transaksi -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-300 animate-in fade-in slide-in-from-bottom duration-500" style="animation-delay: 0.3s;">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Setor</p>
                </div>
                <div class="p-3 bg-purple-50 rounded-lg text-purple-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012-2"></path></svg>
                </div>
            </div>
            <h3 class="text-3xl font-bold text-gray-900">{{ $totalTransactions }} Kali</h3>
        </div>
    </div>

    <!-- Statistik Setoran + Aktivitas Terakhir -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Statistik Setoran (2/3 width) -->
        <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-gray-100 animate-in fade-in slide-in-from-left duration-500">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                <div>
                    <h3 class="font-bold text-lg text-gray-900">Statistik Setoran</h3>
                    <p id="chartTitle" class="text-sm text-gray-500 mt-1">-</p>
                </div>
                <div class="flex items-center gap-2">
                    <div class="flex border border-gray-200 rounded-lg overflow-hidden shadow-sm">
                        <button id="prevBtn" class="p-2 hover:bg-gray-50 border-r border-gray-200 transition-colors" title="Sebelumnya">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        </button>
                        <button id="nextBtn" class="p-2 hover:bg-gray-50 transition-colors" title="Selanjutnya">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    </div>
                    <select id="chartFilter" class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none bg-white shadow-sm">
                        <option value="daily">Harian (7 hari)</option>
                        <option value="weekly">Tahunan (12 bulan)</option>
                    </select>
                </div>
            </div>

            <!-- Loading Spinner -->
            <div id="chartLoading" class="hidden absolute inset-0 bg-white bg-opacity-50 flex items-center justify-center z-10 rounded-xl">
                <div class="flex flex-col items-center gap-2">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-emerald-600"></div>
                    <span class="text-sm text-gray-600">Memuat data...</span>
                </div>
            </div>

            <div class="relative h-72 w-full">
                <canvas id="setoranChart"></canvas>
            </div>
        </div>

        <!-- Aktivitas Terakhir (1/3 width) -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 animate-in fade-in slide-in-from-right duration-500">
            <h3 class="font-bold text-lg text-gray-900 mb-6">Aktivitas Terakhir</h3>
            
            <div class="space-y-4">
                @forelse($latestActivities as $activity)
                    <div class="flex gap-3 animate-in fade-in duration-300">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-8 w-8 rounded-lg {{ $activity['title'] == 'Menyetor sampah' ? 'bg-emerald-50' : ($activity['status'] == 'PENDING' ? 'bg-yellow-50' : ($activity['status'] == 'FAILED' ? 'bg-red-50' : 'bg-emerald-50')) }}">
                                <svg class="w-5 h-5 {{ $activity['title'] == 'Menyetor sampah' ? 'text-emerald-600' : ($activity['status'] == 'PENDING' ? 'text-yellow-600' : ($activity['status'] == 'FAILED' ? 'text-red-600' : 'text-emerald-600')) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @if($activity['title'] == 'Menyetor sampah')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 19l-7-7m0 0l7-7m-7 7h16"></path>
                                    @endif
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">{{ $activity['title'] }}</p>
                            <p class="text-lg font-bold text-gray-800">Rp {{ number_format($activity['amount'], 0, ',', '.') }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $activity['created_at']->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-8 text-center">
                        <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="text-sm text-gray-500">Belum ada aktivitas.</p>
                    </div>
                @endforelse

                <a href="{{ route('nasabah.history.index') }}" class="block text-center text-emerald-600 text-sm font-medium mt-4 hover:text-emerald-700 hover:underline transition-colors">Lihat Semua Riwayat →</a>
            </div>
        </div>
    </div>
</div>

<script>
    const formatIDR = (val) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(val);

    let currentOffset = 0;
    let currentPeriod = 'daily';
    let setoranChart = null;
    let isLoading = false;

    const ctx = document.getElementById('setoranChart').getContext('2d');

    // Chart Options
    const chartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { 
            legend: { display: false },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                titleFont: { size: 13, weight: 'bold' },
                bodyFont: { size: 12 },
                borderColor: 'rgba(0, 0, 0, 0.2)',
                borderWidth: 1,
                cornerRadius: 8,
                callbacks: {
                    label: function(context) {
                        return formatIDR(context.raw);
                    }
                }
            }
        },
        scales: {
            y: { 
                beginAtZero: true, 
                grid: { borderDash: [2, 4], color: 'rgba(0, 0, 0, 0.05)' },
                ticks: {
                    callback: function(value) {
                        if (value >= 1000000) {
                            return (value / 1000000).toFixed(0) + 'M';
                        } else if (value >= 1000) {
                            return (value / 1000).toFixed(0) + 'K';
                        }
                        return value;
                    },
                    color: '#6B7280'
                }
            },
            x: { 
                grid: { display: false },
                ticks: { color: '#6B7280' }
            }
        }
    };

    // Initialize Chart
    function initChart(data) {
        if (setoranChart) {
            setoranChart.destroy();
        }

        setoranChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Setoran (Rp)',
                    data: data.amount,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 2.5,
                    pointRadius: 4,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverRadius: 6,
                    tension: 0.4,
                    fill: true,
                    segment: {
                        borderColor: ctx => ctx.p0DataIndex === undefined || ctx.p1DataIndex === undefined ? 'transparent' : '#10b981'
                    }
                }]
            },
            options: chartOptions
        });
    }

    // Update Chart via AJAX
    async function updateChart() {
        if (isLoading) return;
        
        isLoading = true;
        document.getElementById('chartLoading').classList.remove('hidden');

        try {
            const response = await fetch(`{{ route('nasabah.dashboard.chart') }}?type=${currentPeriod}&offset=${currentOffset}`);
            const data = await response.json();
            
            document.getElementById('chartTitle').innerText = data.title;
            initChart(data);
        } catch (error) {
            console.error('Error fetching chart data:', error);
            alert('Gagal memuat data. Silakan coba lagi.');
        } finally {
            isLoading = false;
            document.getElementById('chartLoading').classList.add('hidden');
        }
    }

    // Event Listeners
    document.getElementById('prevBtn').addEventListener('click', () => {
        if (currentPeriod === 'daily') {
            currentOffset--;
        } else {
            currentOffset--;
        }
        updateChart();
    });

    document.getElementById('nextBtn').addEventListener('click', () => {
        if (currentPeriod === 'daily') {
            currentOffset++;
        } else {
            currentOffset++;
        }
        updateChart();
    });

    document.getElementById('chartFilter').addEventListener('change', (e) => {
        currentPeriod = e.target.value;
        currentOffset = 0;
        updateChart();
    });

    // Initial Load
    updateChart();
</script>
@endsection
