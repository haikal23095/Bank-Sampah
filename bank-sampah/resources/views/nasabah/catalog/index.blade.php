@extends('layouts.app')

@section('title', 'Katalog Harga Sampah')

@section('content')
<div class="max-w-7xl mx-auto">

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Katalog Harga Sampah</h1>
        <p class="text-gray-500 text-sm mt-1">Daftar jenis sampah yang diterima beserta harga per kilogram.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        @forelse($categories as $category)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col h-full">

            <div class="flex items-start gap-4 mb-6">
                <div class="w-12 h-12 bg-emerald-600 rounded-xl flex items-center justify-center flex-shrink-0 text-white shadow-md">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">{{ $category->name }}</h3>
                    <p class="text-xs text-gray-500 line-clamp-2">{{ $category->description ?? 'Berbagai jenis ' . strtolower($category->name) }}</p>
                </div>
            </div>

            <div class="space-y-3 flex-1">
                @forelse($category->wasteTypes as $item)
                    <div class="border border-gray-100 rounded-xl p-3 hover:border-emerald-200 transition bg-white">
                        <p class="text-sm font-semibold text-gray-700 mb-1">{{ $item->name }}</p>
                        <p class="text-emerald-600 font-bold text-sm">
                            Rp {{ number_format($item->price_per_kg, 0, ',', '.') }}
                            <span class="text-gray-400 font-normal text-xs">/{{ $item->unit }}</span>
                        </p>
                    </div>
                @empty
                    <div class="text-center py-8 border border-dashed border-gray-200 rounded-xl bg-gray-50">
                        <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path></svg>
                        <p class="text-xs text-gray-400 italic">Tidak ada item ditemukan</p>
                    </div>
                @endforelse
            </div>
        </div>
        @empty
        <div class="col-span-full py-12 text-center bg-white rounded-2xl border border-gray-100 shadow-sm">
            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <h3 class="text-gray-900 font-bold">Belum ada kategori sampah</h3>
            <p class="text-gray-500 text-sm mt-1">Katalog akan ditampilkan segera setelah tersedia.</p>
        </div>
        @endforelse

    </div>
</div>
@endsection
