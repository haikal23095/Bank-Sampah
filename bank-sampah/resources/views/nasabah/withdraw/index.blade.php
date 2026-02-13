@extends('layouts.app')

@section('title', 'Tarik Saldo')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left column: Billing info + Wallet card -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Info Billing Nasabah</h3>
                </div>

                <form action="{{ route('nasabah.billing.update') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="text-xs text-gray-500 font-semibold">NAMA BANK</label>
                        <input type="text" name="bank_name" value="{{ old('bank_name', $user->bank_name) }}" placeholder="Belum diatur"
                               class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm bg-white" />
                    </div>

                    <div class="mb-3">
                        <label class="text-xs text-gray-500 font-semibold">NOMOR REKENING</label>
                        <input type="text" name="account_number" value="{{ old('account_number', $user->account_number) }}" placeholder="Belum diatur"
                               class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm bg-white" />
                    </div>

                    <button type="submit" class="w-full bg-emerald-50 text-emerald-600 font-semibold rounded-lg py-2.5 mt-2">
                        <span class="inline-flex items-center gap-2 justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Simpan Rekening
                        </span>
                    </button>
                </form>
            </div>

            <div class="bg-emerald-600 text-white rounded-2xl p-6 shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs">Total Saldo Tersedia</p>
                        <p class="text-2xl font-bold mt-2">Rp {{ number_format(optional($wallet)->balance ?? 0, 0, ',', '.') }}</p>
                    </div>
                    <div class="text-sm bg-emerald-700 px-3 py-1 rounded-full">EcoBank Wallet</div>
                </div>
            </div>
        </div>

        <!-- Right column: Withdraw form + last requests (span two cols on lg) -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Ajukan Penarikan Saldo</h3>

                @if(session('success'))
                    <div class="mb-4 bg-green-50 border border-green-100 text-green-700 px-4 py-2 rounded">{{ session('success') }}</div>
                @endif

                <form id="withdrawForm" action="{{ route('nasabah.withdraw.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="method" id="methodInput" value="tunai">

                    <div class="grid grid-cols-3 gap-4 items-center mb-4">
                        <div class="col-span-2">
                            <label class="text-sm text-gray-600 font-medium">Metode Penarikan</label>
                            <div class="flex gap-3 mt-3">
                                <button type="button" data-method="tunai" class="method-btn flex-1 rounded-xl border border-gray-100 px-4 py-3 text-sm text-gray-600 bg-white"> 
                                    <div class="flex flex-col items-center">
                                        <svg class="w-6 h-6 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2"/></svg>
                                        <span class="font-semibold">Tunai (Cash)</span>
                                    </div>
                                </button>

                                <button type="button" data-method="transfer" class="method-btn flex-1 rounded-xl border border-gray-100 px-4 py-3 text-sm text-gray-600 bg-white">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-6 h-6 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1"/></svg>
                                        <span class="font-semibold">Transfer Bank</span>
                                    </div>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="text-sm text-gray-600 font-medium">Jumlah Penarikan (Rp)</label>
                            <input type="text" name="amount_display" id="amountInput" placeholder="Minimal 10.000" class="mt-2 w-full rounded-xl border border-gray-100 px-4 py-3 bg-gray-50 text-gray-600 font-semibold" />
                            <input type="hidden" name="amount" id="amountHidden" />
                        </div>
                    </div>

                    <div id="transferWarning" class="hidden mb-4 p-3 bg-red-50 border border-red-100 text-red-700 rounded">Lengkapi info bank Anda di kolom kiri sebelum menarik via transfer.</div>

                    <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white py-3 rounded-xl shadow-md text-lg font-semibold">Ajukan Penarikan Sekarang</button>
                </form>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Permintaan Penarikan Terakhir</h3>
                @if($withdrawals->isEmpty())
                    <p class="text-gray-400 italic">Belum ada permintaan penarikan.</p>
                @else
                    <div class="space-y-3">
                        @foreach($withdrawals as $w)
                            <div class="flex items-center justify-between border border-gray-100 rounded-xl p-3">
                                <div>
                                    <p class="font-semibold">Rp {{ number_format($w->amount, 0, ',', '.') }}</p>
                                    <p class="text-xs text-gray-500">{{ ucfirst($w->method) }} — {{ $w->date->format('d M Y') }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $w->status === 'PENDING' ? 'bg-yellow-100 text-yellow-700' : ($w->status === 'SUCCESS' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700') }}">{{ $w->status }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    // FORMAT RUPIAH
    const amountInput = document.querySelector('#amountInput');
    const amountHidden = document.querySelector('#amountHidden');

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

    // Method toggle logic
    document.querySelectorAll('.method-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const method = this.getAttribute('data-method');
            document.getElementById('methodInput').value = method;
            document.querySelectorAll('.method-btn').forEach(b => b.classList.remove('border-emerald-300', 'bg-emerald-50'));
            this.classList.add('border-emerald-300', 'bg-emerald-50');

            // Show warning if transfer selected but no billing info
            const hasBank = "{{ $user->bank_name || $user->account_number ? '1' : '' }}";
            const warningEl = document.getElementById('transferWarning');
            if (method === 'transfer' && !hasBank) {
                warningEl.classList.remove('hidden');
            } else {
                warningEl.classList.add('hidden');
            }
        });
    });

    // Set default selected to 'tunai' on load
    window.addEventListener('DOMContentLoaded', function() {
        const defaultBtn = document.querySelector('.method-btn[data-method="tunai"]');
        if (defaultBtn) defaultBtn.click();
    });
</script>
@endsection
