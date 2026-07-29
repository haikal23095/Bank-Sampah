<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Sampah - @yield('title')</title>
    {{-- <script src="https://cdn.tailwindcss.com"></script> --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        .active-nav { background-color: #ecfdf5; color: #059669; border-right: 3px solid #059669; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex flex-col">

    <!-- Top Navbar -->
    <nav class="sticky top-0 bg-slate-900 border-b border-slate-800/80 z-50 shadow-md shadow-slate-950/20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo & Left Nav Links -->
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ strtoupper(Auth::user()->role) === 'ADMIN' ? route('admin.dashboard') : route('nasabah.dashboard') }}" class="flex items-center gap-2 text-emerald-400 font-bold text-xl">
                            <img src="{{ asset('img/logo.png') }}" alt="Logo Bank Sampah" class="w-10 h-10 bg-white rounded-full p-1">
                            <span>Bank Sampah</span>
                        </a>
                    </div>
                    <!-- Desktop Nav Links -->
                    <div class="hidden lg:ml-8 lg:flex lg:space-x-2 lg:items-center">
                        @if(strtoupper(Auth::user()->role) === 'ADMIN')
                            <a href="{{ route('admin.dashboard') }}" class="px-3 py-2 rounded-xl text-sm font-medium transition duration-150 {{ request()->routeIs('admin.dashboard') ? 'bg-gradient-to-r from-emerald-600 to-yellow-500 text-white shadow-md shadow-emerald-950/30' : 'text-slate-300 hover:text-emerald-400 hover:bg-slate-800/50' }}">
                                Dashboard
                            </a>
                            <a href="{{ route('admin.deposits.create') }}" class="px-3 py-2 rounded-xl text-sm font-medium transition duration-150 {{ request()->routeIs('admin.deposits.create') ? 'bg-gradient-to-r from-emerald-600 to-yellow-500 text-white shadow-md shadow-emerald-950/30' : 'text-slate-300 hover:text-emerald-400 hover:bg-slate-800/50' }}">
                                Setor Sampah
                            </a>
                            <a href="{{ route('admin.customers.index') }}" class="px-3 py-2 rounded-xl text-sm font-medium transition duration-150 {{ request()->routeIs('admin.customers.index') ? 'bg-gradient-to-r from-emerald-600 to-yellow-500 text-white shadow-md shadow-emerald-950/30' : 'text-slate-300 hover:text-emerald-400 hover:bg-slate-800/50' }}">
                                Kelola Nasabah
                            </a>
                            <a href="/admin/penarikan" class="px-3 py-2 rounded-xl text-sm font-medium transition duration-150 {{ request()->routeIs('admin.withdrawals.index') ? 'bg-gradient-to-r from-emerald-600 to-yellow-500 text-white shadow-md shadow-emerald-950/30' : 'text-slate-300 hover:text-emerald-400 hover:bg-slate-800/50' }}">
                                Permintaan Penarikan
                            </a>
                            <a href="{{ route('admin.history.index') }}" class="px-3 py-2 rounded-xl text-sm font-medium transition duration-150 {{ request()->routeIs('admin.history.*') ? 'bg-gradient-to-r from-emerald-600 to-yellow-500 text-white shadow-md shadow-emerald-950/30' : 'text-slate-300 hover:text-emerald-400 hover:bg-slate-800/50' }}">
                                Riwayat
                            </a>
                            <a href="{{ route('admin.catalog.index') }}" class="px-3 py-2 rounded-xl text-sm font-medium transition duration-150 {{ request()->routeIs('admin.catalog.*') ? 'bg-gradient-to-r from-emerald-600 to-yellow-500 text-white shadow-md shadow-emerald-950/30' : 'text-slate-300 hover:text-emerald-400 hover:bg-slate-800/50' }}">
                                Katalog
                            </a>
                        @else
                            <a href="{{ route('nasabah.dashboard') }}" class="px-3 py-2 rounded-xl text-sm font-medium transition duration-150 {{ request()->routeIs('nasabah.index', 'nasabah.dashboard') ? 'bg-gradient-to-r from-emerald-600 to-yellow-500 text-white shadow-md shadow-emerald-950/30' : 'text-slate-300 hover:text-emerald-400 hover:bg-slate-800/50' }}">
                                Dashboard
                            </a>
                            <a href="{{ route('nasabah.history.index') }}" class="px-3 py-2 rounded-xl text-sm font-medium transition duration-150 {{ request()->routeIs('nasabah.history.*') ? 'bg-gradient-to-r from-emerald-600 to-yellow-500 text-white shadow-md shadow-emerald-950/30' : 'text-slate-300 hover:text-emerald-400 hover:bg-slate-800/50' }}">
                                Riwayat
                            </a>
                            <a href="{{ route('nasabah.withdraw.index') }}" class="px-3 py-2 rounded-xl text-sm font-medium transition duration-150 {{ request()->routeIs('nasabah.withdraw.*') ? 'bg-gradient-to-r from-emerald-600 to-yellow-500 text-white shadow-md shadow-emerald-950/30' : 'text-slate-300 hover:text-emerald-400 hover:bg-slate-800/50' }}">
                                Tarik Saldo
                            </a>
                            <a href="{{ route('nasabah.catalog.index') }}" class="px-3 py-2 rounded-xl text-sm font-medium transition duration-150 {{ request()->routeIs('nasabah.catalog.*') ? 'bg-gradient-to-r from-emerald-600 to-yellow-500 text-white shadow-md shadow-emerald-950/30' : 'text-slate-300 hover:text-emerald-400 hover:bg-slate-800/50' }}">
                                Katalog
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Desktop Right: Profile & Logout -->
                <div class="hidden lg:flex lg:items-center lg:gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-emerald-500 to-yellow-400 text-white flex items-center justify-center font-bold shadow-md shadow-emerald-950/30">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <div class="text-left">
                            <p class="text-xs font-semibold text-slate-200 leading-3">{{ Auth::user()->name }}</p>
                            <p class="text-[10px] text-slate-400 capitalize leading-3 mt-1.5">{{ Auth::user()->role }}</p>
                        </div>
                    </div>
                    <div class="h-6 w-px bg-slate-800"></div>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                        @csrf
                    </form>
                    <button type="button" onclick="openLogoutModal()" class="cursor-pointer flex items-center gap-1.5 text-rose-400 hover:text-rose-300 hover:bg-rose-950/30 text-sm font-medium px-3.5 py-2 rounded-xl transition duration-150">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Keluar
                    </button>
                </div>

                <!-- Mobile Hamburger Button -->
                <div class="flex items-center lg:hidden">
                    <button onclick="toggleMobileMenu()" class="p-2.5 rounded-xl text-slate-400 hover:text-emerald-400 hover:bg-slate-800/50 transition duration-150">
                        <svg id="hamburger-icon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Dropdown -->
        <div id="mobile-menu" class="lg:hidden border-t border-slate-800 bg-slate-900 shadow-inner max-h-0 opacity-0 overflow-hidden transition-all duration-300 ease-in-out">
            <div class="px-3 pt-2 pb-3 space-y-1.5">
                @if(strtoupper(Auth::user()->role) === 'ADMIN')
                    <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.dashboard') ? 'bg-gradient-to-r from-emerald-600 to-yellow-500 text-white' : 'text-slate-300 hover:bg-slate-800/50' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('admin.deposits.create') }}" class="block px-4 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.deposits.create') ? 'bg-gradient-to-r from-emerald-600 to-yellow-500 text-white' : 'text-slate-300 hover:bg-slate-800/50' }}">
                        Setor Sampah
                    </a>
                    <a href="{{ route('admin.customers.index') }}" class="block px-4 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.customers.index') ? 'bg-gradient-to-r from-emerald-600 to-yellow-500 text-white' : 'text-slate-300 hover:bg-slate-800/50' }}">
                        Kelola Nasabah
                    </a>
                    <a href="/admin/penarikan" class="block px-4 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.withdrawals.index') ? 'bg-gradient-to-r from-emerald-600 to-yellow-500 text-white' : 'text-slate-300 hover:bg-slate-800/50' }}">
                        Permintaan Penarikan
                    </a>
                    <a href="{{ route('admin.history.index') }}" class="block px-4 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.history.*') ? 'bg-gradient-to-r from-emerald-600 to-yellow-500 text-white' : 'text-slate-300 hover:bg-slate-800/50' }}">
                        Riwayat
                    </a>
                    <a href="{{ route('admin.catalog.index') }}" class="block px-4 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.catalog.*') ? 'bg-gradient-to-r from-emerald-600 to-yellow-500 text-white' : 'text-slate-300 hover:bg-slate-800/50' }}">
                        Katalog
                    </a>
                @else
                    <a href="{{ route('nasabah.dashboard') }}" class="block px-4 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('nasabah.index', 'nasabah.dashboard') ? 'bg-gradient-to-r from-emerald-600 to-yellow-500 text-white' : 'text-slate-300 hover:bg-slate-800/50' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('nasabah.history.index') }}" class="block px-4 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('nasabah.history.*') ? 'bg-gradient-to-r from-emerald-600 to-yellow-500 text-white' : 'text-slate-300 hover:bg-slate-800/50' }}">
                        Riwayat
                    </a>
                    <a href="{{ route('nasabah.withdraw.index') }}" class="block px-4 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('nasabah.withdraw.*') ? 'bg-gradient-to-r from-emerald-600 to-yellow-500 text-white' : 'text-slate-300 hover:bg-slate-800/50' }}">
                        Tarik Saldo
                    </a>
                    <a href="{{ route('nasabah.catalog.index') }}" class="block px-4 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('nasabah.catalog.*') ? 'bg-gradient-to-r from-emerald-600 to-yellow-500 text-white' : 'text-slate-300 hover:bg-slate-800/50' }}">
                        Katalog
                    </a>
                @endif
            </div>

            <!-- Profile Info in Mobile Dropdown -->
            <div class="px-4 py-3 border-t border-slate-800 bg-slate-950/50 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-500 to-yellow-400 text-white flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-200 leading-none">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-slate-400 capitalize mt-1.5 leading-none">{{ Auth::user()->role }}</p>
                    </div>
                </div>
                <button type="button" onclick="openLogoutModal()" class="cursor-pointer flex items-center gap-1 text-rose-450 hover:text-rose-300 hover:bg-rose-950/30 px-3 py-1.5 rounded-lg transition">
                    Keluar
                </button>
            </div>
        </div>
    </nav>

    <!-- Main Content Area -->
    <main class="flex-grow p-4 lg:p-8 max-w-7xl w-full mx-auto flex flex-col">
        <div class="flex-grow">
            @yield('content')
        </div>

        <footer class="pt-10 pb-6 text-center">
            <div class="inline-block p-1 px-3 rounded-full bg-slate-900 border border-slate-800/80 shadow-md">
                <p class="text-xs text-slate-500">
                    &copy; {{ date('Y') }} <span class="text-emerald-400 font-semibold">Bank Sampah</span> 
                    <span class="mx-2 text-slate-700">|</span> 
                    Sistem Informasi Bank Sampah Terintegrasi
                </p>
            </div>
        </footer>
    </main>

    <!-- Global Logout Confirmation Modal -->
    <div id="logoutModal" class="fixed inset-0 bg-black/80 z-[100] hidden flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl shadow-2xl w-full max-w-sm overflow-hidden transform transition-all scale-95 opacity-0" id="logoutContent">
            <div class="p-8 text-center">
                <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full mb-6 bg-rose-950/30 text-rose-400">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                </div>
                <h3 class="text-2xl font-bold text-white mb-2">Yakin Ingin Keluar?</h3>
                <p class="text-slate-400 mb-8 px-4 leading-relaxed">Sesi Anda akan berakhir dan Anda harus masuk kembali untuk mengakses akun.</p>
                <div class="flex gap-3">
                    <button onclick="closeLogoutModal()" class="w-full py-3 rounded-xl font-bold text-slate-300 bg-slate-800 hover:bg-slate-700 transition-all active:scale-95">
                        Batal
                    </button>
                    <button onclick="document.getElementById('logout-form').submit()" class="w-full py-3 rounded-xl font-bold text-white bg-gradient-to-r from-rose-500 to-red-600 hover:from-rose-600 hover:to-red-700 shadow-lg shadow-rose-950/30 transition-all active:scale-95">
                        Ya, Keluar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let isMobileMenuOpen = false;

        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            isMobileMenuOpen = !isMobileMenuOpen;
            if (isMobileMenuOpen) {
                menu.classList.remove('max-h-0', 'opacity-0');
                menu.classList.add('max-h-[500px]', 'opacity-100');
            } else {
                menu.classList.remove('max-h-[500px]', 'opacity-100');
                menu.classList.add('max-h-0', 'opacity-0');
            }
        }

        function openLogoutModal() {
            const modal = document.getElementById('logoutModal');
            const content = document.getElementById('logoutContent');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeLogoutModal() {
            const modal = document.getElementById('logoutModal');
            const content = document.getElementById('logoutContent');
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 300);
        }

        // Close on click outside
        document.getElementById('logoutModal').addEventListener('click', (e) => {
            if (e.target.id === 'logoutModal') closeLogoutModal();
        });
    </script>

</body>
</html>