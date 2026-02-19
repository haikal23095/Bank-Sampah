@extends('layouts.app')

@section('title', 'Setor Sampah')

@section('content')
<div class="max-w-4xl mx-auto">
    
    <form action="{{ route('admin.deposits.store') }}" method="POST" id="depositForm">
        @csrf
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center border-b border-gray-100 pb-4 mb-4 gap-3">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Form Setoran Sampah</h2>
                    <p class="text-gray-500 text-sm">Input data penimbangan sampah nasabah di sini.</p>
                </div>
                <div class="bg-green-50 text-green-700 px-4 py-2 rounded-lg font-bold text-lg w-full md:w-auto text-center">
                    Total: <span id="displayTotalRp">Rp 0</span>
                </div>
            </div>

            <div class="mb-6 relative" id="nasabah-search-container">
                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Nasabah</label>
                <div class="relative">
                    <input type="text" id="nasabah-search-input" placeholder="Ketik nama atau email nasabah..." autocomplete="off" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none pr-10">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </div>
                <!-- Hidden input for the actual user_id -->
                <input type="hidden" name="user_id" id="user_id_input" required>
                
                <!-- Dropdown suggestions -->
                <div id="nasabah-results" class="hidden absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                    <!-- Results will be injected here via JS -->
                </div>
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
                <div class="item-row grid grid-cols-1 md:grid-cols-12 gap-x-4 gap-y-2 items-end bg-gray-50 p-3 rounded-lg border border-gray-100 relative">
                    <div class="col-span-1 md:col-span-5 relative waste-search-container">
                        <label class="text-xs text-gray-500 mb-1 block">Jenis Sampah</label>
                        <input type="text" placeholder="Cari jenis sampah..." autocomplete="off" class="waste-search-input w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500" required>
                        <input type="hidden" name="items[0][waste_type_id]" class="waste-type-id" required data-price="0">
                        <div class="waste-results hidden absolute z-40 w-full mt-1 bg-white border border-gray-200 rounded shadow-lg max-h-40 overflow-y-auto"></div>
                    </div>
                    <div class="col-span-1 md:col-span-3">
                        <label class="text-xs text-gray-500 mb-1 block">Berat (kg/liter/pcs)</label>
                        <input type="number" step="0.1" name="items[0][weight]" oninput="calculateRow(this)" class="weight-input w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500" placeholder="0.0" required>
                    </div>
                    <div class="col-span-1 md:col-span-3">
                        <label class="text-xs text-gray-500 mb-1 block">Subtotal</label>
                        <input type="text" class="subtotal-display w-full bg-gray-200 border border-gray-300 rounded px-3 py-2 text-sm text-gray-700 cursor-not-allowed" value="Rp 0" readonly>
                    </div>
                    <div class="hidden md:block md:col-span-1 flex justify-center pb-2">
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div class="flex gap-10">
                    <div>
                        <p class="text-gray-500 text-xs uppercase tracking-wider font-semibold text-[10px]">Total Berat</p>
                        <p class="text-xl font-bold text-gray-800" id="totalWeightDisplay">0.00 kg</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs uppercase tracking-wider font-semibold text-[10px]">Total Saldo Masuk</p>
                        <p class="text-xl font-bold text-green-600" id="totalAmountDisplay">Rp 0</p>
                    </div>
                </div>
                <button type="submit" class="w-full md:w-auto bg-green-600 hover:bg-green-700 text-white px-8 py-3.5 rounded-xl shadow-lg font-bold flex items-center justify-center gap-2 transition active:scale-95 shadow-green-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                    Simpan Transaksi
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Notification Modal -->
<div id="notificationModal" class="fixed inset-0 bg-black bg-opacity-60 z-[60] hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden transform transition-all scale-95 opacity-0" id="notificationContentModal">
        <div class="p-8 text-center border-b border-gray-50">
            <div id="notificationIcon" class="mx-auto flex items-center justify-center h-20 w-20 rounded-full mb-6">
                <!-- Icon via JS -->
            </div>
            <h3 id="notificationTitle" class="text-2xl font-bold text-gray-900 mb-2"></h3>
            <p id="notificationMessage" class="text-gray-500 mb-8 px-4 leading-relaxed"></p>
            <button onclick="closeNotification()" id="notificationButton" class="w-full py-3.5 rounded-xl font-bold text-white shadow-lg transition-all active:scale-95">
                Mengerti
            </button>
        </div>
    </div>
</div>

<script>
    // Notification Logic
    const notificationModal = document.getElementById('notificationModal');
    const notificationContentModal = document.getElementById('notificationContentModal');
    const notificationIcon = document.getElementById('notificationIcon');
    const notificationTitle = document.getElementById('notificationTitle');
    const notificationMessage = document.getElementById('notificationMessage');
    const notificationButton = document.getElementById('notificationButton');

    function showNotification(type, title, message) {
        notificationTitle.textContent = title;
        notificationMessage.textContent = message;

        if (type === 'success') {
            notificationIcon.className = "mx-auto flex items-center justify-center h-20 w-20 rounded-full mb-6 bg-green-100 text-green-600";
            notificationIcon.innerHTML = `<svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>`;
            notificationButton.className = "w-full py-3.5 rounded-xl font-bold text-white shadow-lg transition-all active:scale-95 bg-emerald-500 hover:bg-emerald-600 shadow-emerald-200";
        } else {
            notificationIcon.className = "mx-auto flex items-center justify-center h-20 w-20 rounded-full mb-6 bg-red-100 text-red-600";
            notificationIcon.innerHTML = `<svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>`;
            notificationButton.className = "w-full py-3.5 rounded-xl font-bold text-white shadow-lg transition-all active:scale-95 bg-red-500 hover:bg-red-600 shadow-red-200";
        }

        notificationModal.classList.remove('hidden');
        setTimeout(() => {
            notificationContentModal.classList.remove('scale-95', 'opacity-0');
            notificationContentModal.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeNotification() {
        notificationContentModal.classList.remove('scale-100', 'opacity-100');
        notificationContentModal.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            notificationModal.classList.add('hidden');
        }, 300);
    }

    // Auto-trigger based on session
    @if(session('success'))
        showNotification('success', 'Berhasil!', "{{ session('success') }}");
    @endif
    @if(session('error'))
        showNotification('error', 'Gagal!', "{{ session('error') }}");
    @endif
    @if($errors->any())
        showNotification('error', 'Gagal!', "{{ implode(', ', $errors->all()) }}");
    @endif

    let itemCount = 1;

    // Fungsi Tambah Baris Baru
    function addItem() {
        const container = document.getElementById('items-container');
        const index = itemCount++;
        
        const html = `
            <div class="item-row grid grid-cols-1 md:grid-cols-12 gap-x-4 gap-y-2 items-end bg-gray-50 p-4 rounded-xl border border-gray-100 mt-4 relative" id="row-${index}">
                <div class="col-span-1 md:col-span-5 relative waste-search-container">
                    <label class="text-[10px] font-bold text-gray-400 uppercase mb-1 block">Jenis Sampah</label>
                    <input type="text" placeholder="Cari jenis sampah..." autocomplete="off" class="waste-search-input w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500" required>
                    <input type="hidden" name="items[${index}][waste_type_id]" class="waste-type-id" required data-price="0">
                    <div class="waste-results hidden absolute z-40 w-full mt-1 bg-white border border-gray-200 rounded shadow-lg max-h-40 overflow-y-auto"></div>
                </div>
                <div class="col-span-1 md:col-span-3">
                    <label class="text-[10px] font-bold text-gray-400 uppercase mb-1 block">Berat</label>
                    <input type="number" step="0.1" name="items[${index}][weight]" oninput="calculateRow(this)" class="weight-input w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500" placeholder="0.0" required>
                </div>
                <div class="col-span-1 md:col-span-3">
                    <label class="text-[10px] font-bold text-gray-400 uppercase mb-1 block">Subtotal</label>
                    <input type="text" class="subtotal-display w-full bg-gray-200 border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-700 cursor-not-allowed font-medium" value="Rp 0" readonly>
                </div>
                <div class="absolute top-2 right-2 md:relative md:top-auto md:right-auto md:col-span-1 flex justify-center">
                    <button type="button" onclick="removeRow(${index})" class="text-gray-300 hover:text-red-500 p-1.5 rounded-lg hover:bg-red-50 transition">
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
        const hiddenInput = row.querySelector('.waste-type-id');
        const weightInput = row.querySelector('.weight-input');
        const subtotalDisplay = row.querySelector('.subtotal-display');

        const price = parseFloat(hiddenInput.dataset.price) || 0;
        const weight = parseFloat(weightInput.value) || 0;
        
        const subtotal = price * weight;
        
        // Format Rupiah
        subtotalDisplay.value = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(subtotal);
        
        calculateGrandTotal();
    }

    // Fungsi Hitung Total Keseluruhan
    function calculateGrandTotal() {
        let totalWeight = 0;
        let totalPrice = 0;

        document.querySelectorAll('.item-row').forEach(row => {
            const hiddenInput = row.querySelector('.waste-type-id');
            const weightInput = row.querySelector('.weight-input');
            
            const price = parseFloat(hiddenInput.dataset.price) || 0;
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

    // --- Autocomplete Logic Nasabah ---
    const nasabahs = @json($nasabahs);
    const wasteTypes = @json($wasteTypes);
    
    // logic nasabah search... (existing)
    const searchInput = document.getElementById('nasabah-search-input');
    const resultsDiv = document.getElementById('nasabah-results');
    const userIdInput = document.getElementById('user_id_input');
    const searchContainer = document.getElementById('nasabah-search-container');

    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        userIdInput.value = ''; 
        
        if (query.length === 0) {
            resultsDiv.classList.add('hidden');
            return;
        }

        const filtered = nasabahs.filter(n => 
            n.name.toLowerCase().includes(query) || 
            n.email.toLowerCase().includes(query)
        ).slice(0, 10); 

        resultsDiv.innerHTML = '';
        if (filtered.length > 0) {
            filtered.forEach(n => {
                const div = document.createElement('div');
                div.className = "px-4 py-2 hover:bg-green-50 cursor-pointer border-b border-gray-50 last:border-b-0 group";
                div.innerHTML = `
                    <p class="font-bold text-gray-800 group-hover:text-green-700">${n.name}</p>
                    <p class="text-xs text-gray-500">${n.email}</p>
                `;
                div.addEventListener('click', () => {
                    searchInput.value = `${n.name} - ${n.email}`;
                    userIdInput.value = n.id;
                    resultsDiv.classList.add('hidden');
                });
                resultsDiv.appendChild(div);
            });
            resultsDiv.classList.remove('hidden');
        } else {
            const div = document.createElement('div');
            div.className = 'px-4 py-3 text-sm text-gray-500 italic';
            div.innerText = 'Tidak ditemukan nasabah';
            resultsDiv.appendChild(div);
            resultsDiv.classList.remove('hidden');
        }
    });

    // --- Autocomplete Logic Waste Type (Generic for all rows) ---
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('waste-search-input')) {
            const input = e.target;
            const container = input.closest('.waste-search-container');
            const resultsDiv = container.querySelector('.waste-results');
            const hiddenInput = container.querySelector('.waste-type-id');
            const query = input.value.toLowerCase().trim();

            hiddenInput.value = '';
            hiddenInput.dataset.price = '0';
            calculateRow(input); // Recalculate with 0 price if search changed

            if (query.length === 0) {
                resultsDiv.classList.add('hidden');
                return;
            }

            const filtered = wasteTypes.filter(t => 
                t.name.toLowerCase().includes(query)
            ).slice(0, 10);

            resultsDiv.innerHTML = '';
            if (filtered.length > 0) {
                filtered.forEach(t => {
                    const div = document.createElement('div');
                    div.className = "px-4 py-2 hover:bg-green-50 cursor-pointer border-b border-gray-50 last:border-b-0 group text-sm";
                    div.innerHTML = `
                        <p class="font-semibold text-gray-800 group-hover:text-green-700">${t.name}</p>
                        <p class="text-xs text-gray-500">Rp ${new Intl.NumberFormat('id-ID').format(t.price_per_kg)}/kg</p>
                    `;
                    div.addEventListener('click', () => {
                        input.value = t.name;
                        hiddenInput.value = t.id;
                        hiddenInput.dataset.price = t.price_per_kg;
                        resultsDiv.classList.add('hidden');
                        calculateRow(input);
                    });
                    resultsDiv.appendChild(div);
                });
                resultsDiv.classList.remove('hidden');
            } else {
                const div = document.createElement('div');
                div.className = 'px-4 py-2 text-xs text-gray-500 italic';
                div.innerText = 'Tidak ditemukan';
                resultsDiv.appendChild(div);
                resultsDiv.classList.remove('hidden');
            }
        }
    });

    // Close all results when clicking outside
    document.addEventListener('click', function(e) {
        // Nasabah
        if (!searchContainer.contains(e.target)) {
            resultsDiv.classList.add('hidden');
        }
        
        // Waste Types
        if (!e.target.closest('.waste-search-container')) {
            document.querySelectorAll('.waste-results').forEach(el => el.classList.add('hidden'));
        }
    });
</script>
@endsection