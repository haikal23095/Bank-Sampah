@extends('layouts.app')

@section('title', 'Katalog Harga Sampah')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Katalog Harga Sampah</h1>
        <p class="text-gray-500 mt-2">Daftar jenis sampah yang diterima beserta harga per kilogram.</p>
    </div>

    <!-- Categories Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($categories as $category)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                <!-- Category Header with Icon -->
                <div class="bg-gradient-to-br from-green-500 to-green-600 p-6 text-white">
                    <div class="flex items-center gap-3">
                        <!-- Icon -->
                        <div class="p-3 bg-white bg-opacity-20 rounded-lg">
                            @switch($category->id)
                                @case(1)
                                    <!-- Plastik Icon -->
                                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 2C7.897 2 7 2.897 7 4v3H5c-1.103 0-2 .897-2 2v11c0 1.103.897 2 2 2h14c1.103 0 2-.897 2-2V9c0-1.103-.897-2-2-2h-2V4c0-1.103-.897-2-2-2H9zM9 4h6v3H9V4zm7 6h1v9h-1v-9zm-3 0h1v9h-1v-9zm-3 0h1v9H10v-9zM5 9h14v11H5V9z"/>
                                    </svg>
                                @break
                                @case(2)
                                    <!-- Kertas Icon -->
                                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-8-6zM16 18H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V4.5L19.5 9H13z"/>
                                    </svg>
                                @break
                                @case(3)
                                    <!-- Logam Icon -->
                                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5zm0 2.18L19.65 7H4.35L12 4.18zm0 15.32c-4.01 0-7.35-3.34-7.35-7.35S7.99 4.8 12 4.8s7.35 3.34 7.35 7.35-3.34 7.35-7.35 7.35z"/>
                                    </svg>
                                @break
                                @case(4)
                                    <!-- Kaca Icon -->
                                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M9.5 2a.5.5 0 0 0-.5.5V4h-2V2.5a.5.5 0 0 0-1 0V4H4a1 1 0 0 0-1 1v16a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1h-2V2.5a.5.5 0 0 0-1 0V4h-2V2.5a.5.5 0 0 0-1 0V4H9.5V2.5a.5.5 0 0 0-.5-.5zM20 5v14H4V5h16z"/>
                                    </svg>
                                @break
                                @case(5)
                                    <!-- Organik Icon -->
                                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2L9.5 8h5L12 2zm6 6l-4.5 8h4l4.5-8h-4zm-12 0l-4.5 8h4l4.5-8h-4zm6 10l-4.5 3h9l-4.5-3h0z"/>
                                    </svg>
                                @break
                                @default
                                    <!-- Default Box Icon -->
                                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z"/>
                                    </svg>
                            @endswitch
                        </div>
                        <div>
                            <h2 class="font-bold text-lg">{{ $category->name }}</h2>
                            @if ($category->description)
                                <p class="text-xs text-green-100">{{ $category->description }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Waste Types List -->
                <div class="p-6">
                    @forelse ($category->wasteTypes as $wasteType)
                        <div class="mb-5 pb-5 border-b border-gray-100 last:mb-0 last:pb-0 last:border-0">
                            <div class="flex justify-between items-start gap-3">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-800">{{ $wasteType->name }}</h3>
                                    <p class="text-xs text-gray-500">Satuan: {{ $wasteType->unit }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-bold text-green-600">
                                        Rp {{ number_format($wasteType->price_per_kg, 0, ',', '.') }}
                                    </p>
                                    <p class="text-xs text-gray-400">/kg</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-400 py-8">
                            <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"></path>
                            </svg>
                            Belum ada jenis sampah
                        </p>
                    @endempty
                </div>
            </div>
        @endforeach
    </div>

    <!-- Empty State -->
    @if ($categories->isEmpty())
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"></path>
            </svg>
            <p class="text-gray-500 mb-2">Belum ada kategori sampah</p>
            <p class="text-sm text-gray-400">Katalog akan ditampilkan segera setelah tersedia</p>
        </div>
    @endif
</div>
@endsection
