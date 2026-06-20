<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use App\Models\Order;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 🌟 FIX MAKSIMAL: Mencegah Badai Kueri Menggunakan Trik Static Cache
        View::composer('*', function ($view) {
            static $cachedCount = null; // Menyimpan hitungan di memori request

            if ($cachedCount === null) {
                if (Schema::hasTable('orders')) {
                    // Query ke MySQL HANYA dijalankan satu kali saja di awal
                    $cachedCount = Order::where('status', 'pending')->count();
                } else {
                    $cachedCount = 0;
                }
            }

            // Bagikan hasil hitungan yang instan ke semua halaman
            $view->with('newOrdersCount', $cachedCount);
        });
    }
}