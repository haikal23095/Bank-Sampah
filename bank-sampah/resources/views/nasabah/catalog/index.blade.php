@extends('layouts.app')

@section('title', 'Katalog Harga Sampah')

@section('content')
<div class="max-w-7xl mx-auto">

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Katalog Harga Sampah</h1>
            <p class="text-gray-500 text-sm mt-1">Daftar jenis sampah yang diterima beserta harga per kilogram.</p>
        </div>
        <div class="w-full md:w-auto">
            <div class="relative group">
                <input type="text" id="catalogSearchInput" placeholder="Cari nama atau kategori..." 
                    class="w-full sm:w-64 pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all outline-none text-sm shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 group-focus-within:text-emerald-500 transition-colors pointer-events-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <button id="clearSearch" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-red-500 hidden">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="catalogGrid">

        @forelse($categories as $category)
        <div class="category-card bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col h-full" 
             data-category-name="{{ strtolower($category->name) }}">

            <div class="flex items-start gap-4 mb-6">
                <div class="w-12 h-12 bg-emerald-600 rounded-xl flex items-center justify-center flex-shrink-0 text-white shadow-md">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 category-title">{{ $category->name }}</h3>
                    <p class="text-xs text-gray-500 line-clamp-2">{{ $category->description ?? 'Berbagai jenis ' . strtolower($category->name) }}</p>
                </div>
            </div>

            <div class="space-y-3 flex-1 items-container">
                @forelse($category->wasteTypes as $item)
                    <div class="waste-item border border-gray-100 rounded-xl p-3 hover:border-emerald-200 transition bg-white"
                         data-item-name="{{ strtolower($item->name) }}">
                        <p class="text-sm font-semibold text-gray-700 mb-1 item-name">{{ $item->name }}</p>
                        <p class="text-emerald-600 font-bold text-sm">
                            Rp {{ number_format($item->price_per_kg, 0, ',', '.') }}
                            <span class="text-gray-400 font-normal text-xs">/{{ $item->unit }}</span>
                        </p>
                    </div>
                @empty
                    <div class="text-center py-8 border border-dashed border-gray-200 rounded-xl bg-gray-50 empty-message">
                        <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path></svg>
                        <p class="text-xs text-gray-400 italic">Tidak ada item ditemukan</p>
                    </div>
                @endforelse
            </div>
        </div>
        @empty
        <div class="col-span-full py-12 text-center bg-white rounded-2xl border border-gray-100 shadow-sm" id="emptyState">
            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <h3 class="text-gray-900 font-bold">Belum ada kategori sampah</h3>
            <p class="text-gray-500 text-sm mt-1">Katalog akan ditampilkan segera setelah tersedia.</p>
        </div>
        @endforelse

        <!-- Client-side No Results State -->
        <div class="col-span-full py-12 text-center bg-white rounded-2xl border border-gray-100 shadow-sm hidden" id="noResultsJS">
            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <h3 class="text-gray-900 font-bold">Hasil tidak ditemukan</h3>
            <p class="text-gray-500 text-sm mt-1">Tidak ada kategori atau jenis sampah yang sesuai dengan pencarian Anda.</p>
            <button onclick="resetSearch()" class="mt-4 text-emerald-600 font-medium hover:underline text-sm focus:outline-none">Hapus Pencarian</button>
        </div>

    </div>
</div>

<script>
    const searchInput = document.getElementById('catalogSearchInput');
    const clearBtn = document.getElementById('clearSearch');
    const catalogGrid = document.getElementById('catalogGrid');
    const categoryCards = document.querySelectorAll('.category-card');
    const noResultsJS = document.getElementById('noResultsJS');

    function filterCatalog() {
        const query = searchInput.value.toLowerCase().trim();
        let anyVisible = false;

        // Toggle clear button
        clearBtn.classList.toggle('hidden', query === '');

        categoryCards.forEach(card => {
            const categoryName = card.dataset.categoryName;
            const wasteItems = card.querySelectorAll('.waste-item');
            
            let categoryMatches = categoryName.includes(query);
            let anyItemMatches = false;

            wasteItems.forEach(item => {
                const itemName = item.dataset.itemName;
                const itemMatches = itemName.includes(query);
                
                if (itemMatches) {
                    item.classList.remove('hidden');
                    anyItemMatches = true;
                } else {
                    // If the CATEGORY matches, we might want to show all items? 
                    // Usually better to hide non-matching items unless query is empty
                    if (categoryMatches && query !== '') {
                        item.classList.remove('hidden'); // Show all items if category title matches
                        anyItemMatches = true;
                    } else if (categoryMatches && query === '') {
                        item.classList.remove('hidden');
                    } else {
                        item.classList.add('hidden');
                    }
                }
            });

            // Show card if category title matches OR any item inside matches
            if (categoryMatches || anyItemMatches) {
                card.classList.remove('hidden');
                anyVisible = true;
                
                // If items are empty or all hidden, show the "empty message" within card
                const emptyMsg = card.querySelector('.empty-message');
                if (emptyMsg) {
                    // if category matches but has no items at all
                    emptyMsg.classList.toggle('hidden', anyItemMatches || categoryMatches);
                }
            } else {
                card.classList.add('hidden');
            }
        });

        // Show/hide the global "No Results" message
        if (noResultsJS) {
            noResultsJS.classList.toggle('hidden', anyVisible || categoryCards.length === 0);
        }
    }

    function resetSearch() {
        searchInput.value = '';
        filterCatalog();
        searchInput.focus();
    }

    if (searchInput) {
        searchInput.addEventListener('input', filterCatalog);
    }

    if (clearBtn) {
        clearBtn.addEventListener('click', resetSearch);
    }
</script>
@endsection
