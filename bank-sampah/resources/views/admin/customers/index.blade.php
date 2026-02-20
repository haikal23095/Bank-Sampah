@extends('layouts.app')

@section('title', 'Kelola Nasabah')

@section('content')
<div class="max-w-6xl mx-auto">

    <div class="flex justify-between items-start mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Kelola Nasabah</h1>
            <p class="text-gray-500 text-sm mt-1">Manajemen akun dan informasi nasabah.</p>
        </div>
        <button onclick="openModal()" class="bg-emerald-500 hover:bg-emerald-600 text-white px-5 py-2.5 rounded-lg font-medium flex items-center gap-2 shadow-sm transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Nasabah
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        
        <div class="p-5 border-b border-gray-100 bg-white">
            <div class="relative max-w-sm">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" id="searchInput" placeholder="Cari nama, email, atau telepon..." class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[750px]">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                        <th class="px-6 py-4 font-semibold">Nama / Info</th>
                        <th class="px-6 py-4 font-semibold">Kontak</th>
                        <th class="px-6 py-4 font-semibold">Info Bank</th>
                        <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" id="customerTableBody">
                    @forelse($customers as $customer)
                    <tr class="customer-row hover:bg-gray-50 transition" 
                        data-search="{{ strtolower($customer->name . ' ' . $customer->email . ' ' . $customer->phone . ' ' . ($customer->address ?? '')) }}">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 font-bold">
                                    {{ substr($customer->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900">{{ $customer->name }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5 truncate max-w-[150px]">
                                        {{ $customer->address ?? 'Alamat belum diisi' }}
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-700">{{ $customer->email }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $customer->phone }}</p>
                        </td>
                        <td class="px-6 py-4">
                            @if($customer->bank_name)
                                <p class="text-sm text-gray-700 font-medium">{{ $customer->bank_name }}</p>
                                <p class="text-xs text-gray-500">{{ $customer->account_number }}</p>
                            @else
                                <span class="text-xs italic text-gray-400">Belum diisi</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button onclick="openEditModal({{ $customer }})" class="text-blue-500 hover:text-blue-700 p-1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </button>
                                <button type="button" onclick="confirmDelete({{ $customer->id }})" class="text-red-500 hover:text-red-700 p-1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                                <form id="delete-form-{{ $customer->id }}" action="{{ route('admin.customers.destroy', $customer->id) }}" method="POST" class="hidden">
                                    @csrf @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr id="noDataRow">
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                            Belum ada data nasabah.
                        </td>
                    </tr>
                    @endforelse
                    <tr id="noResultsRow" class="hidden">
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                            Pencarian tidak ditemukan.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="modalOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden transform transition-all scale-95 opacity-0" id="modalContent">
        
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h3 class="text-lg font-bold text-gray-800">Tambah Nasabah Baru</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form action="{{ route('admin.customers.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" name="name" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none" placeholder="Contoh: Budi Santoso">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" required pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$" title="Format email tidak valid (contoh: user@gmail.com)" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none" placeholder="email@contoh.com">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
                    <input type="text" name="phone" required oninput="formatPhoneNumber(this)" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none" placeholder="0812-1234-5678">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none" placeholder="Minimal 6 karakter">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <select name="role" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none bg-white">
                        <option value="nasabah" selected>Nasabah</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat (Opsional)</label>
                    <input type="text" name="address" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none" placeholder="Kota / Jalan">
                </div>
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <button type="button" onclick="closeModal()" class="px-4 py-2 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition">Batal</button>
                <button type="submit" class="px-4 py-2 text-white bg-emerald-500 hover:bg-emerald-600 rounded-lg font-medium shadow-md transition">Simpan Nasabah</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Nasabah -->
<div id="editModalOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden transform transition-all scale-95 opacity-0" id="editModalContent">
        
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h3 class="text-lg font-bold text-gray-800">Edit Informasi Nasabah</h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form id="editForm" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" name="name" id="edit_name" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" id="edit_email" required pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$" title="Format email tidak valid (contoh: user@gmail.com)" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
                    <input type="text" name="phone" id="edit_phone" required oninput="formatPhoneNumber(this)" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password (Kosongkan jika tidak ingin diubah)</label>
                <input type="password" name="password" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none" placeholder="Minimal 6 karakter">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <select name="role" id="edit_role" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none bg-white">
                        <option value="nasabah">Nasabah</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                    <input type="text" name="address" id="edit_address" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                </div>
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition">Batal</button>
                <button type="submit" class="px-4 py-2 text-white bg-blue-500 hover:bg-blue-600 rounded-lg font-medium shadow-md transition">Update Nasabah</button>
            </div>
        </form>
    </div>
</div>

<!-- Notification Modal -->
<div id="notificationModal" class="fixed inset-0 bg-black bg-opacity-60 z-[60] hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden transform transition-all scale-95 opacity-0" id="notificationContent">
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

<!-- Confirm Delete Modal -->
<div id="confirmDeleteModal" class="fixed inset-0 bg-black bg-opacity-60 z-[60] hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden transform transition-all scale-95 opacity-0" id="confirmDeleteContent">
        <div class="p-8 text-center">
            <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full mb-6 bg-red-100 text-red-600">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">Hapus Data?</h3>
            <p class="text-gray-500 mb-8 px-4 leading-relaxed">Apakah Anda yakin ingin menghapus data nasabah ini? Tindakan ini tidak dapat dibatalkan.</p>
            <div class="flex gap-3">
                <button onclick="closeConfirmDelete()" class="w-full py-3 rounded-xl font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-all active:scale-95">
                    Batal
                </button>
                <button id="finalDeleteBtn" class="w-full py-3 rounded-xl font-bold text-white bg-red-500 hover:bg-red-600 shadow-lg shadow-red-200 transition-all active:scale-95">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    const modalOverlay = document.getElementById('modalOverlay');
    const modalContent = document.getElementById('modalContent');
    const editModalOverlay = document.getElementById('editModalOverlay');
    const editModalContent = document.getElementById('editModalContent');

    function openModal() {
        modalOverlay.classList.remove('hidden');
        setTimeout(() => {
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeModal() {
        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modalOverlay.classList.add('hidden');
        }, 300);
    }

    function openEditModal(customer) {
        // Set values
        document.getElementById('editForm').action = `/admin/nasabah/${customer.id}`;
        document.getElementById('edit_name').value = customer.name;
        document.getElementById('edit_email').value = customer.email;
        document.getElementById('edit_phone').value = customer.phone;
        document.getElementById('edit_role').value = customer.role;
        document.getElementById('edit_address').value = customer.address || '';

        // Show modal
        editModalOverlay.classList.remove('hidden');
        setTimeout(() => {
            editModalContent.classList.remove('scale-95', 'opacity-0');
            editModalContent.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeEditModal() {
        editModalContent.classList.remove('scale-100', 'opacity-100');
        editModalContent.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            editModalOverlay.classList.add('hidden');
        }, 300);
    }

    // Format Phone Number Function
    function formatPhoneNumber(input) {
        // Remove all non-numeric characters
        let value = input.value.replace(/\D/g, '');
        
        // Limit to 12 digits
        if (value.length > 12) {
            value = value.slice(0, 12);
        }
        
        // Format as 4-4-4 pattern (e.g., 0812-1234-5678)
        let formatted = '';
        for (let i = 0; i < value.length; i++) {
            if (i > 0 && i % 4 === 0) {
                formatted += '-';
            }
            formatted += value[i];
        }
        
        input.value = formatted;
    }

    // Tutup modal jika klik di luar area konten
    modalOverlay.addEventListener('click', function(e) {
        if (e.target === modalOverlay) closeModal();
    });

    editModalOverlay.addEventListener('click', function(e) {
        if (e.target === editModalOverlay) closeEditModal();
    });

    // Notification Logic
    const notificationModal = document.getElementById('notificationModal');
    const notificationContent = document.getElementById('notificationContent');
    const notificationIcon = document.getElementById('notificationIcon');
    const notificationTitle = document.getElementById('notificationTitle');
    const notificationMessage = document.getElementById('notificationMessage');
    const notificationButton = document.getElementById('notificationButton');

    function showNotification(type, title, message) {
        notificationTitle.textContent = title;
        notificationMessage.textContent = message;

        if (type === 'success') {
            notificationIcon.className = "mx-auto flex items-center justify-center h-20 w-20 rounded-full mb-6 bg-green-100 text-green-600";
            notificationIcon.innerHTML = '<svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
            notificationButton.className = "w-full py-3 rounded-xl font-bold text-white shadow-lg transition-transform active:scale-95 bg-emerald-500 hover:bg-emerald-600 shadow-emerald-200";
        } else {
            notificationIcon.className = "mx-auto flex items-center justify-center h-20 w-20 rounded-full mb-6 bg-red-100 text-red-600";
            notificationIcon.innerHTML = '<svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
            notificationButton.className = "w-full py-3 rounded-xl font-bold text-white shadow-lg transition-transform active:scale-95 bg-red-500 hover:bg-red-600 shadow-red-200";
        }

        notificationModal.classList.remove('hidden');
        setTimeout(() => {
            notificationContent.classList.remove('scale-95', 'opacity-0');
            notificationContent.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeNotification() {
        notificationContent.classList.remove('scale-100', 'opacity-100');
        notificationContent.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            notificationModal.classList.add('hidden');
        }, 300);
    }

    // Delete Confirmation Logic
    const confirmDeleteModal = document.getElementById('confirmDeleteModal');
    const confirmDeleteContent = document.getElementById('confirmDeleteContent');
    const finalDeleteBtn = document.getElementById('finalDeleteBtn');
    let currentDeleteId = null;

    function confirmDelete(id) {
        currentDeleteId = id;
        confirmDeleteModal.classList.remove('hidden');
        setTimeout(() => {
            confirmDeleteContent.classList.remove('scale-95', 'opacity-0');
            confirmDeleteContent.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeConfirmDelete() {
        confirmDeleteContent.classList.remove('scale-100', 'opacity-100');
        confirmDeleteContent.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            confirmDeleteModal.classList.add('hidden');
        }, 300);
    }

    finalDeleteBtn.addEventListener('click', function() {
        if (currentDeleteId) {
            document.getElementById('delete-form-' + currentDeleteId).submit();
        }
    });

    // Auto-trigger based on session
    @if(session('success'))
        showNotification('success', 'Berhasil!', "{{ session('success') }}");
    @endif

    @if($errors->any())
        showNotification('error', 'Gagal!', "{{ implode(', ', $errors->all()) }}");
    @endif

    // Real-time Search Filtering
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.getElementById('customerTableBody');
    const customerRows = document.querySelectorAll('.customer-row');
    const noResultsRow = document.getElementById('noResultsRow');

    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        let anyVisible = false;

        customerRows.forEach(row => {
            const data = row.dataset.search;
            if (data.includes(query)) {
                row.classList.remove('hidden');
                anyVisible = true;
            } else {
                row.classList.add('hidden');
            }
        });

        // Toggle "no results" message
        if (anyVisible || query === '') {
            noResultsRow.classList.add('hidden');
        } else {
            noResultsRow.classList.remove('hidden');
        }
    });
</script>

@endsection