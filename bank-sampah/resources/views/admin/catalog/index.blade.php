@extends('layouts.app')

@section('title', 'Katalog Harga Sampah')

@section('content')
<div class="max-w-7xl mx-auto">
    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Katalog Harga Sampah</h1>
            <p class="text-gray-500 text-sm mt-1">Daftar jenis sampah yang diterima beserta harga per kilogram.</p>
        </div>
        <div class="flex flex-col sm:flex-row w-full md:w-auto gap-3">
            <form action="{{ route('admin.catalog.index') }}" method="GET" class="relative group">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau kategori..." 
                    class="w-full sm:w-64 pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all outline-none text-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-emerald-500 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </form>
            <button onclick="openManageCategoriesModal()" class="border border-emerald-500 text-emerald-600 hover:bg-emerald-50 px-5 py-2.5 rounded-lg font-medium transition flex items-center justify-center gap-2 whitespace-nowrap">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                Kelola Kategori
            </button>
            <button onclick="openModal()" class="bg-emerald-500 hover:bg-emerald-600 text-white px-5 py-2.5 rounded-lg font-medium shadow-md flex items-center justify-center gap-2 transition whitespace-nowrap">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Tambah Jenis Sampah
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        
        @forelse($categories as $category)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col h-full">
            
            <div class="flex items-start gap-4 mb-6">
                <div class="w-12 h-12 bg-emerald-600 rounded-xl flex items-center justify-center flex-shrink-0 text-white shadow-emerald-200 shadow-md">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">{{ $category->name }}</h3>
                    <p class="text-xs text-gray-500 line-clamp-2">{{ $category->description ?? 'Berbagai jenis ' . strtolower($category->name) }}</p>
                </div>
            </div>

            <div class="space-y-3 flex-1">
                @forelse($category->wasteTypes as $item)
                    <div class="border border-gray-100 rounded-xl p-3 hover:border-emerald-200 transition bg-white group relative">
                        <form action="{{ route('admin.catalog.destroyType', $item->id) }}" method="POST" onsubmit="return confirm('Hapus item ini?')" class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition">
                            @csrf @method('DELETE')
                            <button class="text-gray-300 hover:text-red-500"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                        </form>

                        <p class="text-sm font-semibold text-gray-700 mb-1 {{ request()->filled('search') && str_contains(strtolower($item->name), strtolower(request('search'))) ? 'bg-yellow-100' : '' }}">{{ $item->name }}</p>
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
            <h3 class="text-gray-900 font-bold">Pencarian Tidak Ditemukan</h3>
            <p class="text-gray-500 text-sm mt-1">Kata kunci "{{ request('search') }}" tidak cocok dengan kategori atau jenis sampah manapun.</p>
            <a href="{{ route('admin.catalog.index') }}" class="mt-4 inline-block text-emerald-600 font-medium hover:underline text-sm">Reset Pencarian</a>
        </div>
        @endforelse

    </div>
</div>

<div id="modalAdd" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4 backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform scale-95 opacity-0 transition-all duration-300" id="modalContent">
        
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800">Tambah Jenis Sampah</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form action="{{ route('admin.catalog.storeType') }}" method="POST" class="p-6 space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1.5">Nama Jenis Sampah</label>
                <input type="text" name="name" required placeholder="Contoh: Botol Plastik PET" 
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1.5">Kategori</label>
                <div class="relative">
                    <select name="category_id" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 outline-none appearance-none bg-white">
                        <option value="" disabled selected>Pilih Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-gray-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5">Harga (Rp)</label>
                    <input type="text" name="price_per_kg_display" required placeholder="0" 
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 outline-none" id="price_per_kg_display">
                    <input id="price_per_kg" name="price_per_kg" type="hidden" value="0">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5">Satuan</label>
                    <div class="relative">
                        <select name="unit" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 outline-none appearance-none bg-white">
                            <option value="kg">kg</option>
                            <option value="pcs">pcs</option>
                            <option value="liter">liter</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-gray-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-lg shadow-lg flex items-center justify-center gap-2 transition transform hover:scale-[1.02]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                    Tambah Jenis
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Kelola Kategori -->
<div id="modalManageCategories" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4 backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl transform scale-95 opacity-0 transition-all duration-300 overflow-hidden" id="modalManageContent">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h3 class="text-lg font-bold text-gray-800">Kelola Kategori Sampah</h3>
            <button onclick="closeManageCategoriesModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="p-6">
            <!-- Form Tambah Kategori -->
            <form action="{{ route('admin.catalog.storeCategory') }}" method="POST" class="mb-8 p-4 bg-emerald-50 rounded-xl border border-emerald-100">
                @csrf
                <h4 class="text-sm font-bold text-emerald-800 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Kategori Baru
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <input type="text" name="name" required placeholder="Nama Kategori (misal: Plastik)" 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>
                    <div class="flex gap-2">
                        <input type="text" name="description" placeholder="Deskripsi singkat..." 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                        <button type="submit" class="bg-emerald-600 h text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-emerald-700 transition flex-shrink-0">
                            Simpan
                        </button>
                    </div>
                </div>
            </form>

            <!-- Daftar Kategori -->
            <h4 class="text-sm font-bold text-gray-700 mb-3">Daftar Kategori</h4>
            <div class="max-h-64 overflow-y-auto pr-2 custom-scrollbar">
                <table class="w-full text-left">
                    <thead class="text-xs text-gray-500 uppercase bg-gray-50 sticky top-0">
                        <tr>
                            <th class="px-4 py-2 font-bold">Nama</th>
                            <th class="px-4 py-2 font-bold">Deskripsi</th>
                            <th class="px-4 py-2 font-bold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($categories as $cat)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $cat->name }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500 truncate max-w-[200px]">{{ $cat->description }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-2">
                                    <button onclick="openEditCategoryModal({{ $cat->id }}, '{{ addslashes($cat->name) }}', '{{ addslashes($cat->description) }}')" 
                                        class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <form action="{{ route('admin.catalog.destroyCategory', $cat->id) }}" method="POST" onsubmit="return confirm('Hapus kategori ini? Pastikan tidak ada jenis sampah di dalamnya.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Kategori -->
<div id="modalEditCategory" class="fixed inset-0 bg-black bg-opacity-50 z-[60] hidden flex items-center justify-center p-4 backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform scale-95 opacity-0 transition-all duration-300" id="modalEditCategoryContent">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800">Edit Kategori</h3>
            <button onclick="closeEditCategoryModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form id="editCategoryForm" action="" method="POST" class="p-6 space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1.5">Nama Kategori</label>
                <input type="text" name="name" id="edit_category_name" required 
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 outline-none transition">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1.5">Deskripsi</label>
                <textarea name="description" id="edit_category_description" rows="3" 
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 outline-none transition"></textarea>
            </div>
            <div class="pt-2">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg shadow-lg transition transform hover:scale-[1.02]">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const modal = document.getElementById('modalAdd');
    const modalContent = document.getElementById('modalContent');
    
    // Manage Categories Modals
    const modalManage = document.getElementById('modalManageCategories');
    const modalManageContent = document.getElementById('modalManageContent');
    const modalEditCat = document.getElementById('modalEditCategory');
    const modalEditCatContent = document.getElementById('modalEditCategoryContent');

    function openModal() {
        modal.classList.remove('hidden');
        setTimeout(() => {
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeModal() {
        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    function openManageCategoriesModal() {
        modalManage.classList.remove('hidden');
        setTimeout(() => {
            modalManageContent.classList.remove('scale-95', 'opacity-0');
            modalManageContent.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeManageCategoriesModal() {
        modalManageContent.classList.remove('scale-100', 'opacity-100');
        modalManageContent.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modalManage.classList.add('hidden');
        }, 300);
    }

    function openEditCategoryModal(id, name, description) {
        const form = document.getElementById('editCategoryForm');
        form.action = `/admin/katalog/category/${id}`;
        document.getElementById('edit_category_name').value = name;
        document.getElementById('edit_category_description').value = description;

        modalEditCat.classList.remove('hidden');
        setTimeout(() => {
            modalEditCatContent.classList.remove('scale-95', 'opacity-0');
            modalEditCatContent.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeEditCategoryModal() {
        modalEditCatContent.classList.remove('scale-100', 'opacity-100');
        modalEditCatContent.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modalEditCat.classList.add('hidden');
        }, 300);
    }




    // FORMAT RUPIAH
    const pricePerKgDisplay = document.querySelector('#price_per_kg_display');
    const pricePerKgHidden = document.querySelector('#price_per_kg');

    function formatRupiah(angka, prefix = '') {
        let number_string = angka.replace(/[^,\d]/g, '').toString(),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;

        return rupiah;
    }

    function parseRupiah(value) {
        return parseInt(value.replace(/[^0-9]/g, '')) || 0;
    }

    function updateAmountFormat() {
        console.log('hallooo');
        
        const numericValue = parseRupiah(pricePerKgDisplay.value);
        pricePerKgDisplay.value = formatRupiah(numericValue.toString());
        pricePerKgHidden.value = numericValue;
    }

    // Listener untuk input amount
    pricePerKgDisplay.addEventListener('keyup', updateAmountFormat);
    pricePerKgDisplay.addEventListener('change', updateAmountFormat);

    // Trigger saat load apabila ada old value
    updateAmountFormat();







    // Tutup jika klik background luar
    window.onclick = function(e) {
        if (e.target === modal) closeModal();
        if (e.target === modalManage) closeManageCategoriesModal();
        if (e.target === modalEditCat) closeEditCategoryModal();
    };
</script>
@endsection