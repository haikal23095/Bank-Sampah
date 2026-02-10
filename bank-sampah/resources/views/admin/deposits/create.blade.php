@extends('layouts.app')

@section('title', 'Setor Sampah')

@section('content')
<div class="max-w-4xl mx-auto">
    
    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('admin.deposits.store') }}" method="POST" id="depositForm">
        @csrf
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
            <div class="flex justify-between items-center border-b border-gray-100 pb-4 mb-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Form Setoran Sampah</h2>
                    <p class="text-gray-500 text-sm">Input data penimbangan sampah nasabah di sini.</p>
                </div>
                <div class="bg-green-50 text-green-700 px-4 py-2 rounded-lg font-bold text-lg">
                    Total: <span id="displayTotalRp">Rp 0</span>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Nasabah</label>
                <select name="user_id" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none">
                    <option value="">-- Pilih Nasabah --</option>
                    @foreach($nasabahs as $nasabah)
                        <option value="{{ $nasabah->id }}">{{ $nasabah->name }} - {{ $nasabah->email }}</option>
                    @endforeach
                </select>
            </div>

            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 flex items-start gap-3 mb-6">
                <svg class="w-5 h-5 text-yellow-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <p class="text-sm text-yellow-700">Pastikan timbangan akurat sebelum menyimpan transaksi. Data yang disimpan akan langsung menambah saldo nasabah.</p>
            </div>
            
            <div class="mb-4 flex justify-between items-end">
                <label class="block text-sm font-medium text-gray-700">Daftar Sampah</label>
                <button type="button" onclick="addItem()" class="text-sm text-green-600 font-semibold hover:text-green-800 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Item
                </button>
            </div>

            <div id="items-container" class="space-y-3">
                <div class="item-row grid grid-cols-12 gap-4 items-end bg-gray-50 p-3 rounded-lg border border-gray-100">
                    <div class="col-span-5">
                        <label class="text-xs text-gray-500 mb-1 block">Jenis Sampah</label>
                        <select name="items[0][waste_type_id]" onchange="updatePrice(this)" class="waste-select w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500" required>
                            <option value="" data-price="0">-- Pilih --</option>
                            @foreach($wasteTypes as $type)
                                <option value="{{ $type->id }}" data-price="{{ $type->price_per_kg }}">
                                    {{ $type->name }} (Rp {{ number_format($type->price_per_kg) }}/kg)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-3">
                        <label class="text-xs text-gray-500 mb-1 block">Berat (kg)</label>
                        <input type="number" step="0.1" name="items[0][weight]" oninput="calculateRow(this)" class="weight-input w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500" placeholder="0.0" required>
                    </div>
                    <div class="col-span-3">
                        <label class="text-xs text-gray-500 mb-1 block">Subtotal</label>
                        <input type="text" class="subtotal-display w-full bg-gray-200 border border-gray-300 rounded px-3 py-2 text-sm text-gray-700 cursor-not-allowed" value="Rp 0" readonly>
                    </div>
                    <div class="col-span-1 flex justify-center pb-2">
                        </div>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-100 flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wider font-semibold">Total Berat</p>
                    <p class="text-xl font-bold text-gray-800" id="totalWeightDisplay">0.00 kg</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wider font-semibold">Total Saldo Masuk</p>
                    <p class="text-xl font-bold text-green-600" id="totalAmountDisplay">Rp 0</p>
                </div>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg shadow-md font-medium flex items-center gap-2 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                    Simpan Transaksi
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    let itemCount = 1;

    // Fungsi Tambah Baris Baru
    function addItem() {
        const container = document.getElementById('items-container');
        const index = itemCount++;
        
        const html = `
            <div class="item-row grid grid-cols-12 gap-4 items-end bg-gray-50 p-3 rounded-lg border border-gray-100 mt-2" id="row-${index}">
                <div class="col-span-5">
                    <select name="items[${index}][waste_type_id]" onchange="updatePrice(this)" class="waste-select w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500" required>
                        <option value="" data-price="0">-- Pilih --</option>
                        @foreach($wasteTypes as $type)
                            <option value="{{ $type->id }}" data-price="{{ $type->price_per_kg }}">
                                {{ $type->name }} (Rp {{ number_format($type->price_per_kg) }}/kg)
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-3">
                    <input type="number" step="0.1" name="items[${index}][weight]" oninput="calculateRow(this)" class="weight-input w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500" placeholder="0.0" required>
                </div>
                <div class="col-span-3">
                    <input type="text" class="subtotal-display w-full bg-gray-200 border border-gray-300 rounded px-3 py-2 text-sm text-gray-700 cursor-not-allowed" value="Rp 0" readonly>
                </div>
                <div class="col-span-1 flex justify-center pb-1">
                    <button type="button" onclick="removeRow(${index})" class="text-red-500 hover:text-red-700 p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
    }

    // Fungsi Hapus Baris
    function removeRow(index) {
        document.getElementById(`row-${index}`).remove();
        calculateGrandTotal();
    }

    // Fungsi Hitung per Baris (Saat input berat atau ganti jenis)
    function calculateRow(element) {
        // Cari parent row
        const row = element.closest('.item-row');
        const select = row.querySelector('.waste-select');
        const weightInput = row.querySelector('.weight-input');
        const subtotalDisplay = row.querySelector('.subtotal-display');

        const price = parseFloat(select.options[select.selectedIndex].dataset.price) || 0;
        const weight = parseFloat(weightInput.value) || 0;
        
        const subtotal = price * weight;
        
        // Format Rupiah
        subtotalDisplay.value = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(subtotal);
        
        calculateGrandTotal();
    }

    // Helper wrapper jika select berubah
    function updatePrice(selectElement) {
        calculateRow(selectElement);
    }

    // Fungsi Hitung Total Keseluruhan
    function calculateGrandTotal() {
        let totalWeight = 0;
        let totalPrice = 0;

        document.querySelectorAll('.item-row').forEach(row => {
            const select = row.querySelector('.waste-select');
            const weightInput = row.querySelector('.weight-input');
            
            const price = parseFloat(select.options[select.selectedIndex].dataset.price) || 0;
            const weight = parseFloat(weightInput.value) || 0;

            totalWeight += weight;
            totalPrice += (price * weight);
        });

        // Update UI Summary
        document.getElementById('totalWeightDisplay').innerText = totalWeight.toFixed(2) + ' kg';
        
        const formattedPrice = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(totalPrice);
        document.getElementById('totalAmountDisplay').innerText = formattedPrice;
        document.getElementById('displayTotalRp').innerText = formattedPrice;
    }
</script>
@endsection