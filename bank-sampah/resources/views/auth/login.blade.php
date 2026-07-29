<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Bank Sampah</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 flex items-center justify-center h-screen relative overflow-hidden">
    <!-- Ambient Glows -->
    <div class="absolute -top-40 -left-40 w-96 h-96 rounded-full bg-emerald-500/10 blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 rounded-full bg-yellow-500/10 blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-md bg-slate-900/60 backdrop-blur-xl rounded-3xl shadow-2xl p-8 border border-slate-800/80 relative z-10">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-tr from-emerald-500 to-yellow-400 text-white shadow-lg shadow-emerald-950/40 mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-white">Masuk ke Bank Sampah</h2>
            <p class="text-slate-400 text-sm mt-1">Kelola sampahmu, tabung uangmu.</p>
        </div>

        <form action="{{ route('login.post', [], false) }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-slate-400 mb-1.5">Email Address</label>
                <input type="email" name="email" id="email" required 
                    class="w-full px-4 py-2.5 border border-slate-800 rounded-xl focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 outline-none transition bg-slate-950/50 text-white placeholder-slate-700 focus:bg-slate-950"
                    placeholder="nama@email.com" value="{{ old('email') }}">
                @error('email')
                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-slate-400 mb-1.5">Password</label>
                <input type="password" name="password" id="password" required 
                    class="w-full px-4 py-2.5 border border-slate-800 rounded-xl focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 outline-none transition bg-slate-950/50 text-white placeholder-slate-700 focus:bg-slate-950"
                    placeholder="••••••••">
            </div>

            <button type="submit" 
                class="w-full bg-gradient-to-r from-emerald-600 to-yellow-500 hover:from-emerald-500 hover:to-yellow-400 text-white font-semibold py-3 rounded-xl transition duration-200 shadow-lg shadow-emerald-950/20 active:scale-[0.98]">
                Masuk Sekarang
            </button>
        </form>

        <div class="mt-6 text-center text-sm text-slate-400">
            Belum punya akun? <a href="#" class="text-emerald-400 hover:text-emerald-300 hover:underline font-semibold">Daftar Nasabah</a>
        </div>
    </div>

</body>
</html>