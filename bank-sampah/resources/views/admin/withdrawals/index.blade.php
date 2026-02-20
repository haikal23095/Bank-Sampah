@extends('layouts.app')

@section('title', 'Kelola Penarikan')

@section('content')
<div class="max-w-6xl mx-auto">

    <!-- Confirm Dialog -->
    <div id="confirmActionModal" class="fixed inset-0 bg-black bg-opacity-60 z-[70] hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden transform transition-all scale-95 opacity-0" id="confirmActionContent">
            <div class="p-8 text-center text-gray-800">
                <div id="confirmActionIcon" class="mx-auto flex items-center justify-center h-20 w-20 rounded-full mb-6">
                    <!-- Icon via JS -->
                </div>
                <h3 id="confirmActionTitle" class="text-2xl font-bold mb-2 text-gray-900"></h3>
                <p id="confirmActionMessage" class="text-gray-500 mb-8 px-4 leading-relaxed"></p>
                <div class="flex gap-3">
                    <button onclick="closeConfirmAction()" class="w-full py-3 rounded-xl font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-all active:scale-95">
                        Batal
                    </button>
                    <button id="finalConfirmBtn" class="w-full py-3 rounded-xl font-bold text-white transition-all active:scale-95 shadow-lg">
                        Konfirmasi
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Kelola Permintaan Penarikan</h1>
            <p class="text-gray-500 text-sm mt-1">Tinjau dan proses pengajuan dana nasabah.</p>
        </div>
        <div class="flex flex-wrap gap-3 w-full md:w-auto">
            <button onclick="openModal('modalPending')" class="bg-yellow-100 hover:bg-yellow-200 text-yellow-800 px-4 py-2.5 rounded-xl text-xs font-bold flex items-center transition shadow-sm flex-1 md:flex-none justify-center">
                {{ $pendingWithdrawals->count() }} Permintaan Menunggu
            </button>
            <button onclick="openModal('modalWithdraw')" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold flex items-center justify-center gap-2 shadow-lg shadow-blue-100 transition active:scale-95 flex-1 md:flex-none">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Tambah Penarikan
            </button>
        </div>
    </div>

    <div class="mb-8">
        <!-- Filter Tanggal -->
        <form action="{{ route('admin.withdrawals.index') }}" method="GET" class="flex flex-wrap items-end gap-3 p-4 bg-white rounded-2xl border border-gray-100 shadow-sm">
            <div class="flex flex-col w-full sm:w-auto">
                <label class="text-[10px] font-bold text-gray-400 uppercase ml-1 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 outline-none text-gray-600">
            </div>
            <div class="flex flex-col w-full sm:w-auto">
                <label class="text-[10px] font-bold text-gray-400 uppercase ml-1 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 outline-none text-gray-600">
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white p-2 rounded-lg transition shadow-md shadow-blue-50" title="Filter">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                </button>
                @if(request('start_date') || request('end_date'))
                    <a href="{{ route('admin.withdrawals.index') }}" class="text-xs text-red-500 font-bold hover:underline">Reset</a>
                @endif
            </div>
        </form>

        <div class="mt-4 flex items-center gap-2 text-gray-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="text-[10px] font-black uppercase tracking-widest">Arsip Penarikan Terbaru</span>
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
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[650px]">
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
        </div>
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
                                <button type="button" onclick="confirmApprove({{ $pending->id }})" class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded-lg text-xs font-bold shadow-sm transition">
                                    SETUJUI
                                </button>
                                <form id="approve-form-{{ $pending->id }}" action="{{ route('admin.withdrawals.approve', $pending->id) }}" method="POST" class="hidden">
                                    @csrf
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
            
            <!-- Selection Info (Hidden by default) -->
            <div id="selection-info" class="hidden mb-4 p-3 bg-blue-50 border border-blue-100 rounded-xl animate-in slide-in-from-top-2 duration-300">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-lg" id="info-initial">
                            ?
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-blue-500 uppercase tracking-wider">Nasabah Terpilih</p>
                            <p id="info-name" class="text-sm font-bold text-gray-900"></p>
                            <p id="info-balance" class="text-xs font-medium text-blue-700"></p>
                        </div>
                    </div>
                    <button type="button" onclick="clearSelection()" class="text-xs bg-white border border-blue-200 text-blue-600 px-3 py-1.5 rounded-lg font-bold hover:bg-blue-50 transition shadow-sm">
                        Ubah
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div class="col-span-1 relative" id="nasabah-search-container">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Pilih Nasabah</label>
                    <div class="relative">
                        <input type="text" id="nasabah-search-input" placeholder="Ketik nama atau email..." autocomplete="off" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:border-blue-500 outline-none pr-8">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                    </div>
                    <!-- Hidden input for the actual user_id -->
                    <input type="hidden" name="user_id" id="user_id_input" required>
                    
                    <!-- Dropdown suggestions -->
                    <div id="nasabah-results" class="hidden absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                        <!-- Results injected via JS -->
                    </div>
                </div>
                <div class="col-span-1">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nominal (Rp)</label>
                    <div class="relative">
                        <!-- visible, formatted input -->
                        <input id="amountDisplay" type="text" autocomplete="off" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:border-blue-500 outline-none" placeholder="0">
                        <!-- hidden raw numeric value submitted to server -->
                        <input id="amount" name="amount" type="hidden" value="0">
                    </div>
                    <!-- Error Message for Insufficient Balance -->
                    <div id="amountError" class="hidden mt-2 text-xs text-red-600 font-semibold flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span id="amountErrorText"></span>
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
    // --- Data Nasabah for Autocomplete ---
    const nasabahs = [
        @foreach($nasabahs as $n)
            { 
                id: "{{ $n->id }}", 
                name: "{{ $n->name }}", 
                email: "{{ $n->email }}", 
                balance: "{{ number_format($n->wallet->balance ?? 0, 0, ',', '.') }}",
                bank_name: "{{ $n->bank_name ?? '' }}",
                account_number: "{{ $n->account_number ?? '' }}"
            },
        @endforeach
    ];

    // Autocomplete Nasabah
    const searchContainer = document.getElementById('nasabah-search-container');
    const searchInput = document.getElementById('nasabah-search-input');
    const userIdInput = document.getElementById('user_id_input');
    const resultsDiv = document.getElementById('nasabah-results');
    const selectionInfo = document.getElementById('selection-info');
    const infoName = document.getElementById('info-name');
    const infoBalance = document.getElementById('info-balance');
    const infoInitial = document.getElementById('info-initial');
    
    // Bank Inputs
    const bankNameInput = document.querySelector('input[name="bank_name"]');
    const accountNumberInput = document.querySelector('input[name="account_number"]');
    
    // Store current nasabah balance as numeric value
    let currentNasabahBalance = 0;

    function selectNasabah(n) {
        searchInput.value = n.name;
        userIdInput.value = n.id;
        
        // Store balance as numeric value (remove formatting)
        currentNasabahBalance = parseInt(n.balance.toString().replace(/[^0-9]/g, '')) || 0;
        
        // Populate Bank Info from database
        bankNameInput.value = n.bank_name;
        accountNumberInput.value = n.account_number;
        
        // Show info display
        infoName.innerHTML = `<span class="text-gray-500 font-normal">Nama Nasabah:</span> ${n.name}`;
        infoBalance.textContent = `Saldo: Rp ${n.balance}`;
        infoInitial.textContent = n.name.charAt(0).toUpperCase();
        
        // Hide search input area
        searchContainer.classList.add('opacity-40', 'pointer-events-none');
        selectionInfo.classList.remove('hidden');
        resultsDiv.classList.add('hidden');
        
        // Clear and validate amount
        document.getElementById('amountDisplay').value = '';
        validateAmount();
    }

    function clearSelection() {
        searchInput.value = '';
        userIdInput.value = '';
        currentNasabahBalance = 0;
        
        // Reset Bank Info
        bankNameInput.value = '';
        accountNumberInput.value = '';
        
        // Clear amount and error
        document.getElementById('amountDisplay').value = '';
        document.getElementById('amount').value = '0';
        validateAmount();
        
        selectionInfo.classList.add('hidden');
        searchContainer.classList.remove('opacity-40', 'pointer-events-none');
        searchInput.focus();
    }

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            userIdInput.value = ''; // Reset ID while typing
            
            if (query.length === 0) {
                resultsDiv.classList.add('hidden');
                return;
            }

            const filtered = nasabahs.filter(n => 
                n.name.toLowerCase().includes(query) || n.email.toLowerCase().includes(query)
            ).slice(0, 10);

            resultsDiv.innerHTML = '';
            if (filtered.length > 0) {
                filtered.forEach(n => {
                    const div = document.createElement('div');
                    div.className = "px-4 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-50 last:border-b-0 group text-sm animate-in fade-in duration-200";
                    div.innerHTML = `
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="font-bold text-gray-800 group-hover:text-blue-700">${n.name}</p>
                                <p class="text-xs text-gray-400 group-hover:text-blue-500">${n.email}</p>
                            </div>
                            <span class="text-[10px] font-black text-blue-600 bg-blue-50 px-2 py-1 rounded">Saldo: Rp ${n.balance}</span>
                        </div>
                    `;
                    div.addEventListener('click', () => selectNasabah(n));
                    resultsDiv.appendChild(div);
                });
                resultsDiv.classList.remove('hidden');
            } else {
                const div = document.createElement('div');
                div.className = 'px-4 py-3 text-sm text-gray-400 italic text-center';
                div.innerText = 'Nasabah tidak ditemukan';
                resultsDiv.appendChild(div);
                resultsDiv.classList.remove('hidden');
            }
        });

        // Close on global click
        document.addEventListener('click', (e) => {
            if (!searchContainer.contains(e.target)) resultsDiv.classList.add('hidden');
        });
    }

    // Confirm Dialog Logic
    const confirmActionModal = document.getElementById('confirmActionModal');
    const confirmActionContent = document.getElementById('confirmActionContent');
    const confirmActionIcon = document.getElementById('confirmActionIcon');
    const confirmActionTitle = document.getElementById('confirmActionTitle');
    const confirmActionMessage = document.getElementById('confirmActionMessage');
    const finalConfirmBtn = document.getElementById('finalConfirmBtn');
    let confirmTargetFormId = null;

    function showConfirmModal(title, message, formId, type = 'success') {
        confirmActionTitle.textContent = title;
        confirmActionMessage.textContent = message;
        confirmTargetFormId = formId;

        if (type === 'success') {
            confirmActionIcon.className = "mx-auto flex items-center justify-center h-20 w-20 rounded-full mb-6 bg-emerald-100 text-emerald-600";
            confirmActionIcon.innerHTML = `<svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>`;
            finalConfirmBtn.className = "w-full py-3 rounded-xl font-bold text-white transition-all active:scale-95 shadow-lg bg-emerald-500 hover:bg-emerald-600 shadow-emerald-200 uppercase tracking-wider";
        } else {
            confirmActionIcon.className = "mx-auto flex items-center justify-center h-20 w-20 rounded-full mb-6 bg-red-100 text-red-600";
            confirmActionIcon.innerHTML = `<svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>`;
            finalConfirmBtn.className = "w-full py-3 rounded-xl font-bold text-white transition-all active:scale-95 shadow-lg bg-red-500 hover:bg-red-600 shadow-red-200 uppercase tracking-wider";
        }

        confirmActionModal.classList.remove('hidden');
        setTimeout(() => {
            confirmActionContent.classList.remove('scale-95', 'opacity-0');
            confirmActionContent.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeConfirmAction() {
        confirmActionContent.classList.remove('scale-100', 'opacity-100');
        confirmActionContent.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            confirmActionModal.classList.add('hidden');
        }, 300);
    }

    function confirmApprove(id) {
        showConfirmModal('Setujui Penarikan?', 'Apakah Anda yakin ingin menyetujui permintaan penarikan saldo ini?', 'approve-form-' + id, 'success');
    }

    finalConfirmBtn.addEventListener('click', function() {
        if (confirmTargetFormId) {
            document.getElementById(confirmTargetFormId).submit();
        }
    });

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
    const amountError = document.getElementById('amountError');
    const amountErrorText = document.getElementById('amountErrorText');
    const withdrawSubmitBtn = document.getElementById('withdrawSubmitBtn');

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

    function validateAmount() {
        const numericValue = parseRupiah(amountInput.value);
        
        // Show error if no nasabah selected
        if (currentNasabahBalance === 0 && numericValue > 0) {
            amountError.classList.remove('hidden');
            amountErrorText.textContent = 'Silakan pilih nasabah terlebih dahulu';
            withdrawSubmitBtn.disabled = true;
            withdrawSubmitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            return false;
        }
        
        // Show error if amount exceeds balance
        if (numericValue > 0 && numericValue > currentNasabahBalance) {
            amountError.classList.remove('hidden');
            amountErrorText.textContent = `Nominal melebihi saldo tersedia. Maksimal: Rp ${formatRupiah(currentNasabahBalance.toString())}`;
            withdrawSubmitBtn.disabled = true;
            withdrawSubmitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            return false;
        }

        if (numericValue < 10000) {
            amountError.classList.remove('hidden');
            amountErrorText.textContent = 'Nominal minimal adalah Rp 10.000';
            withdrawSubmitBtn.disabled = true;
            withdrawSubmitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            return false;
        }
            
        // Show error if amount is zero or empty
        if (numericValue === 0) {
            amountError.classList.add('hidden');
            withdrawSubmitBtn.disabled = true;
            withdrawSubmitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            return false;
        }
        
        // Hide error and enable submit if all valid
        amountError.classList.add('hidden');
        withdrawSubmitBtn.disabled = false;
        withdrawSubmitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        return true;
    }

    function updateAmountFormat() {
        const numericValue = parseRupiah(amountInput.value);
        amountInput.value = formatRupiah(numericValue.toString());
        amountHidden.value = numericValue;
        validateAmount();
    }

    // Listener untuk input amount
    amountInput.addEventListener('keyup', updateAmountFormat);
    amountInput.addEventListener('change', updateAmountFormat);

    // Trigger saat load apabila ada old value
    updateAmountFormat();
    
    // Initialize submit button as disabled
    withdrawSubmitBtn.disabled = true;
    withdrawSubmitBtn.classList.add('opacity-50', 'cursor-not-allowed');

</script>
@endsection