@extends('layouts.app')

@section('title', 'Kelola Penarikan')

@section('content')
<div class="max-w-6xl mx-auto">

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Kelola Permintaan Penarikan</h1>
            <p class="text-gray-500 text-sm mt-1">Tinjau dan proses pengajuan dana nasabah.</p>

            <!-- Filter Tanggal -->
            <form action="{{ route('admin.withdrawals.index') }}" method="GET" class="flex items-center gap-3 mt-4">
                <div class="flex flex-col">
                    <label class="text-[10px] font-bold text-gray-400 uppercase ml-1">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="border border-gray-200 rounded-lg px-3 py-1.5 text-xs focus:ring-2 focus:ring-blue-500 outline-none text-gray-600">
                </div>
                <div class="flex flex-col">
                    <label class="text-[10px] font-bold text-gray-400 uppercase ml-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="border border-gray-200 rounded-lg px-3 py-1.5 text-xs focus:ring-2 focus:ring-blue-500 outline-none text-gray-600">
                </div>
                <div class="flex items-end h-full pt-4">
                    <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-600 p-1.5 rounded-lg transition" title="Filter">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    </button>
                    @if(request('start_date') || request('end_date'))
                        <a href="{{ route('admin.withdrawals.index') }}" class="ml-2 text-xs text-red-500 hover:underline mb-2">Reset</a>
                    @endif
                </div>
            </form>

            <div class="mt-6 flex items-center gap-2 text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="text-xs font-medium uppercase tracking-wider">Arsip Penarikan Terbaru</span>
            </div>
        </div>
        <div class="flex gap-3">
            <button onclick="openModal('modalPending')" class="bg-yellow-100 hover:bg-yellow-200 text-yellow-800 px-3 py-2 rounded-lg text-xs font-bold flex items-center transition">
                {{ $pendingWithdrawals->count() }} Permintaan Menunggu
            </button>
            <button onclick="openModal('modalWithdraw')" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-medium flex items-center gap-2 shadow-md transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Tambah Penarikan
            </button>
        </div>
    </div>

    @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
            {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div class="mb-4 bg-emerald-100 border border-emerald-300 text-emerald-700 px-4 py-3 rounded relative">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        @if($withdrawals->count() > 0)
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                        <th class="px-6 py-4 font-semibold">Tanggal</th>
                        <th class="px-6 py-4 font-semibold">Nasabah</th>
                        <th class="px-6 py-4 font-semibold">Metode</th>
                        <th class="px-6 py-4 font-semibold">Nominal (Rp)</th>
                        <th class="px-6 py-4 font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($withdrawals as $trx)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $trx->created_at->format('Y-m-d') }}</td>
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $trx->nasabah->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $trx->method ?? 'CASH' }}</td>
                        <td class="px-6 py-4 font-bold text-gray-800">Rp {{ number_format($trx->amount, 0, ',', '.') }}</td>
                        <td class="px-6 py-4">
                            @if(($trx->status ?? 'PENDING') === 'SUCCESS')
                                <span class="px-2 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-full">BERHASIL</span>
                            @else
                                <span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full">DITOLAK</span>
                                @if($trx->admin_note)
                                    <p class="text-[10px] text-gray-400 mt-1 italic">{{ $trx->admin_note }}</p>
                                @endif
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="flex flex-col items-center justify-center py-20">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4 text-gray-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <p class="text-gray-500">Belum ada riwayat penarikan.</p>
            </div>
        @endif
    </div>
</div>

<div id="modalPending" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl overflow-hidden transform transition-all scale-95 opacity-0 modal-content">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-bold text-yellow-600 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Permintaan Penarikan Menunggu
            </h3>
            <button onclick="closeModal('modalPending')" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>

        <div class="p-6 max-h-[70vh] overflow-y-auto">
            @if($pendingWithdrawals->count() > 0)
                <div class="space-y-4">
                    @foreach($pendingWithdrawals as $pending)
                        <div class="border border-gray-100 rounded-xl p-4 flex justify-between items-center bg-gray-50">
                            <div>
                                <p class="text-sm font-bold text-gray-900">{{ $pending->nasabah->name }}</p>
                                <p class="text-xs text-gray-500">{{ $pending->created_at->format('d M Y, H:i') }} • {{ $pending->method }}</p>
                                <p class="text-lg font-black text-blue-600 mt-1">Rp {{ number_format($pending->amount, 0, ',', '.') }}</p>
                            </div>
                            <div class="flex gap-2">
                                <form action="{{ route('admin.withdrawals.approve', $pending->id) }}" method="POST" onsubmit="return confirm('Setujui penarikan ini?')">
                                    @csrf
                                    <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded-lg text-xs font-bold shadow-sm transition">
                                        SETUJUI
                                    </button>
                                </form>
                                <button onclick="openRejectModal('{{ route('admin.withdrawals.reject', $pending->id) }}')" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-xs font-bold shadow-sm transition">
                                    TOLAK
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-10 text-gray-400">
                    <p>Tidak ada permintaan menunggu.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<div id="modalReject" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden transform transition-all scale-95 opacity-0 modal-content">
        <div class="px-6 py-4 border-b border-gray-100 font-bold text-red-600">Alasan Penolakan</div>
        <form id="rejectForm" method="POST" class="p-6">
            @csrf
            <textarea name="admin_note" required class="w-full border border-gray-300 rounded-lg p-3 text-sm h-32 mb-4" placeholder="Contoh: Saldo tidak mencukupi atau data belum lengkap..."></textarea>
            <div class="flex gap-3">
                <button type="button" onclick="closeModal('modalReject')" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold py-2 rounded-lg transition">Batal</button>
                <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-2 rounded-lg shadow-md transition">Konfirmasi Tolak</button>
            </div>
        </form>
    </div>
</div>

<div id="modalWithdraw" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden transform transition-all scale-95 opacity-0 modal-content">
        
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-bold text-blue-600 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                Penarikan Saldo Manual
            </h3>
            <button onclick="closeModal('modalWithdraw')" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>

        <form id="withdrawForm" action="{{ route('admin.withdrawals.store') }}" method="POST" class="p-6">
            @csrf
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div class="col-span-1">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Pilih Nasabah</label>
                    <select name="user_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:border-blue-500 outline-none">
                        <option value="">-- Cari Nasabah --</option>
                        @foreach($nasabahs as $nasabah)
                            <option value="{{ $nasabah->id }}">{{ $nasabah->name }} (Saldo: Rp {{ number_format($nasabah->wallet->balance ?? 0, 0, ',', '.') }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-1">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nominal (Rp)</label>
                    <div class="relative">
                        <!-- visible, formatted input -->
                        <input id="amountDisplay" type="text" autocomplete="off" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:border-blue-500 outline-none" placeholder="0">
                        <!-- hidden raw numeric value submitted to server -->
                        <input id="amount" name="amount" type="hidden" value="0">
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Metode Pembayaran</label>
                <input type="hidden" name="method" id="methodInput" value="CASH">
                
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" onclick="selectMethod('CASH')" id="btnCash" class="border-2 border-blue-500 bg-blue-50 text-blue-700 rounded-lg py-3 px-4 flex items-center justify-center gap-2 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        Tunai (Cash)
                    </button>
                    <button type="button" onclick="selectMethod('TRANSFER')" id="btnTransfer" class="border border-gray-200 text-gray-500 hover:bg-gray-50 rounded-lg py-3 px-4 flex items-center justify-center gap-2 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path></svg>
                        Transfer Bank
                    </button>
                </div>
            </div>

            <div id="bankInputs" class="hidden grid grid-cols-2 gap-4 mb-6 bg-gray-50 p-4 rounded-lg border border-gray-100">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama Bank</label>
                    <input type="text" name="bank_name" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" placeholder="Contoh: BCA">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">No. Rekening</label>
                    <input type="text" name="account_number" inputmode="numeric" pattern="[0-9]*" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" placeholder="1234xxx">
                </div>
            </div>

            <div class="bg-orange-50 border border-orange-200 rounded-lg p-3 flex gap-3 mb-6">
                <svg class="w-5 h-5 text-orange-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <p class="text-xs text-orange-700 leading-tight">
                    Aksi ini akan langsung memotong saldo nasabah terpilih. Pastikan identitas nasabah dan nominal sudah diverifikasi dengan benar.
                </p>
            </div>

            <button id="withdrawSubmitBtn" type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg shadow-lg flex items-center justify-center gap-2">
                <svg id="withdrawBtnIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span id="withdrawBtnText">Konfirmasi & Cetak Nota</span>
            </button>
        </form>
    </div>
</div>

@if(session('new_withdrawal'))
    @php
        $trxNew = \App\Models\Withdrawal::with('nasabah')->find(session('new_withdrawal'));
    @endphp
    <div id="modalReceipt" class="fixed inset-0 bg-black bg-opacity-70 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-2xl w-full max-w-sm overflow-hidden text-center relative">
            
            <div class="bg-gray-50 pt-8 pb-4 flex justify-center">
                <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
            </div>

            <div class="px-6 pb-6">
                <h3 class="text-lg font-bold text-gray-800">ECOBANK RECEIPT</h3>
                <p class="text-xs text-gray-400 uppercase tracking-widest mb-6">Digital Waste Management System</p>

                <div class="text-left text-sm border-t border-b border-dashed border-gray-300 py-4 space-y-2 mb-6">
                    <div class="flex justify-between">
                        <span class="text-gray-500 text-xs">TRANS ID</span>
                        <span class="text-gray-500 text-xs">{{ $trxNew->id }}-{{ date('Ymd') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 text-xs">TANGGAL</span>
                        <span class="text-gray-500 text-xs">{{ $trxNew->created_at->format('Y-m-d') }}</span>
                    </div>
                    
                    <div class="mt-2">
                        <span class="text-gray-500 text-xs block">NASABAH</span>
                        <span class="font-bold text-gray-800">{{ $trxNew->nasabah->name }}</span>
                    </div>

                    <div class="flex justify-between pt-2">
                        <div>
                            <span class="text-gray-500 text-xs block">METODE</span>
                            <span class="font-bold text-gray-800 uppercase">{{ $trxNew->method ?? 'CASH' }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-gray-500 text-xs block">STATUS</span>
                            <span class="text-emerald-600 font-bold italic">{{ strtoupper($trxNew->status ?? 'PENDING') }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-3 flex justify-between items-center mb-6">
                    <span class="text-xs font-bold text-gray-500 uppercase">Jumlah Penarikan</span>
                    <span class="text-xl font-bold text-gray-900">Rp {{ number_format($trxNew->amount, 0, ',', '.') }}</span>
                </div>

                <p class="text-xs text-gray-400 italic mb-6">"Terima kasih telah berkontribusi menjaga bumi bersama EcoBank. Simpan struk ini sebagai bukti transaksi yang sah."</p>

                <div class="flex gap-2">
                    <button onclick="window.print()" class="flex-1 bg-white border border-gray-300 text-gray-700 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Print
                    </button>
                    <button onclick="document.getElementById('modalReceipt').remove()" class="flex-1 bg-emerald-500 hover:bg-emerald-600 text-white py-2 rounded-lg text-sm font-medium">
                        Selesai
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

<script>
    // --- Logic Modal Form ---
    function openModal(id) {
        const modal = document.getElementById(id);
        const content = modal.querySelector('.modal-content');
        modal.classList.remove('hidden');
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        const content = modal.querySelector('.modal-content');
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    function openRejectModal(url) {
        const form = document.getElementById('rejectForm');
        form.action = url;
        openModal('modalReject');
    }

    // --- Logic Toggle Metode Pembayaran ---
    function selectMethod(method) {
        const input = document.getElementById('methodInput');
        const btnCash = document.getElementById('btnCash');
        const btnTransfer = document.getElementById('btnTransfer');
        const bankInputs = document.getElementById('bankInputs');

        input.value = method;

        if (method === 'CASH') {
            // Style Active Cash
            btnCash.className = "border-2 border-blue-500 bg-blue-50 text-blue-700 rounded-lg py-3 px-4 flex items-center justify-center gap-2 transition";
            // Style Inactive Transfer
            btnTransfer.className = "border border-gray-200 text-gray-500 hover:bg-gray-50 rounded-lg py-3 px-4 flex items-center justify-center gap-2 transition";
            // Hide Bank Inputs
            bankInputs.classList.add('hidden');
        } else {
            // Style Active Transfer
            btnTransfer.className = "border-2 border-blue-500 bg-blue-50 text-blue-700 rounded-lg py-3 px-4 flex items-center justify-center gap-2 transition";
            // Style Inactive Cash
            btnCash.className = "border border-gray-200 text-gray-500 hover:bg-gray-50 rounded-lg py-3 px-4 flex items-center justify-center gap-2 transition";
            // Show Bank Inputs
            bankInputs.classList.remove('hidden');
        }
    }





    // FORMAT RUPIAH
    const amountInput = document.querySelector('#amountDisplay');
    const amountHidden = document.querySelector('#amount');

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
        const numericValue = parseRupiah(amountInput.value);
        amountInput.value = formatRupiah(numericValue.toString());
        amountHidden.value = numericValue;
    }

    // Listener untuk input amount
    amountInput.addEventListener('keyup', updateAmountFormat);
    amountInput.addEventListener('change', updateAmountFormat);

    // Trigger saat load apabila ada old value
    updateAmountFormat();

</script>
@endsection