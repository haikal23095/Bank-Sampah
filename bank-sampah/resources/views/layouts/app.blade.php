<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoBank - @yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        .active-nav { background-color: #ecfdf5; color: #059669; border-right: 3px solid #059669; }
    </style>
</head>
<body class="bg-gray-50">

    <div class="flex h-screen overflow-hidden">
        
        <aside class="w-64 bg-white border-r border-gray-100 fixed h-full flex flex-col justify-between z-10">
            <div>
                <div class="h-20 flex items-center px-8">
                    <a href="{{ strtoupper(Auth::user()->role) === 'ADMIN' ? route('admin.dashboard') : route('nasabah.dashboard') }}" class="flex items-center gap-2 text-green-600 font-bold text-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        <span>EcoBank </span>
                    </a>
                </div>

                <nav class="mt-4 space-y-1">
                    
                    @if(strtoupper(Auth::user()->role) === 'ADMIN')
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-8 py-3 text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'active-nav' : 'text-gray-500 hover:text-green-600 hover:bg-green-50' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                            Dashboard
                        </a>
                        <a href="{{ route('admin.deposits.create') }}" class="flex items-center gap-3 px-8 py-3 text-sm font-medium {{ request()->routeIs('admin.deposits.create') ? 'active-nav' : 'text-gray-500 hover:text-green-600 hover:bg-green-50' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            Setor Sampah
                        </a>
                        <a href="{{ route('admin.customers.index') }}" class="flex items-center gap-3 px-8 py-3 text-sm font-medium {{ request()->routeIs('admin.customers.index') ? 'active-nav' : 'text-gray-500 hover:text-green-600 hover:bg-green-50' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            Kelola Nasabah
                        </a>
                        <a href="/admin/penarikan" class="flex items-center gap-3 px-8 py-3 text-sm font-medium {{ request()->routeIs('admin.withdrawals.index') ? 'active-nav' : 'text-gray-500 hover:text-green-600 hover:bg-green-50' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                            Permintaan Penarikan
                        </a>
                        <a href="{{ route('admin.history.index') }}" class="flex items-center gap-3 px-8 py-3 text-sm font-medium {{ request()->routeIs('admin.history.*') ? 'active-nav' : 'text-gray-500 hover:text-green-600 hover:bg-green-50' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Riwayat
                        </a>
                        <a href="#" class="flex items-center gap-3 px-8 py-3 text-sm font-medium text-gray-500 hover:text-green-600 hover:bg-green-50">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                            Katalog
                        </a>

                    @else
                        <a href="{{ route('nasabah.dashboard') }}" class="flex items-center gap-3 px-8 py-3 text-sm font-medium {{ request()->routeIs('nasabah.index', 'nasabah.dashboard') ? 'active-nav' : 'text-gray-500 hover:text-green-600 hover:bg-green-50' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                            Dashboard
                        </a>
                        <a href="{{ route('nasabah.history.index') }}" class="flex items-center gap-3 px-8 py-3 text-sm font-medium {{ request()->routeIs('nasabah.history.*') ? 'active-nav' : 'text-gray-500 hover:text-green-600 hover:bg-green-50' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Riwayat
                        </a>
                        <a href="{{ route('nasabah.withdraw.index') }}" class="flex items-center gap-3 px-8 py-3 text-sm font-medium {{ request()->routeIs('nasabah.withdraw.*') ? 'active-nav' : 'text-gray-500 hover:text-green-600 hover:bg-green-50' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                            Tarik Saldo
                        </a>
                        <a href="{{ route('nasabah.catalog.index') }}" class="flex items-center gap-3 px-8 py-3 text-sm font-medium {{ request()->routeIs('nasabah.catalog.*') ? 'active-nav' : 'text-gray-500 hover:text-green-600 hover:bg-green-50' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                            Katalog
                        </a>
                    @endif


                </nav>
            </div>

            <div class="p-4 border-t border-gray-100">
                <div class="flex items-center gap-3 mb-4 px-4">
                    <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-bold">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500 capitalize">{{ Auth::user()->role }}</p>
                    </div>
                </div>
                
                <form action="{{ route('logout') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin keluar?')">
                    @csrf
                    <button type="submit" class="flex items-center gap-2 text-red-500 hover:text-red-700 text-sm font-medium px-4 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

        <main class="flex-1 ml-64 p-8 h-screen overflow-y-auto">
            @yield('content')
        </main>
    </div>

</body>
</html>