<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class CacheAllCommand extends Command
{
    protected $signature = 'app:cache-all';
    protected $description = 'Cache config, route, events, views, and prewarm application cache';

    public function handle()
    {
        $this->info("🚀 Starting full cache optimization...");

        // Clear everything first
        Artisan::call('optimize:clear');
        $this->info("🧹 Cleared previous caches.");

        // Cache config
        Artisan::call('config:cache');
        $this->info("📦 Config cached.");

        // Cache routes
        Artisan::call('route:cache');
        $this->info("🛣 Routes cached.");

        // Cache events
        Artisan::call('event:cache');
        $this->info("📅 Events cached.");

        // Cache views
        Artisan::call('view:cache');
        $this->info("🖼 Views cached.");

        // Prewarm application caches (customize as needed)
        $this->prewarmCache();
        $this->info("🔥 Prewarm application cache completed.");

        $this->info("✅ ALL caching tasks finished successfully!");
    }

    private function prewarmCache()
    {
        // Cache::remember('users_all', 3600, fn () => \App\Models\User::all());
        // Cache::remember('settings', 3600, fn () => \App\Models\Setting::first());
        // Cache::remember('dashboard_counts', 3600, fn () => [
        //     'nasabah' => \App\Models\Nasabah::count(),
        //     'transaksi' => \App\Models\Transaksi::count(),
        // ]);

        // Tambahkan prewarm sesuai kebutuhan kamu
    }
}