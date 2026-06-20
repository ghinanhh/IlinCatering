<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB; // 🌟 TAMBAHAN: Biar query DB table menu terlaris terbaca lancar
use App\Models\Menu;
use App\Models\Review; 
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Owner\OwnerController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\GoogleCalendarController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- 1. HALAMAN PUBLIK (Landing Page) ---
Route::get('/', function () {
    // 🌟 REVISI DINAMIS: Hitung total porsi terjual dari tabel order_items & orders agar otomatis sinkron ke halaman depan
    $topMenuIds = DB::table('order_items')
        ->join('orders', 'order_items.order_id', '=', 'orders.id')
        ->whereNotIn('orders.status', ['batal', 'canceled', 'pending']) // Hanya hitung dari pesanan yang sah/valid
        ->select('order_items.menu_id', DB::raw('SUM(order_items.quantity) as total_terjual'))
        ->groupBy('order_items.menu_id')
        ->orderByDesc('total_terjual')
        ->take(4) // Ambil peringkat 4 besar terlaris
        ->pluck('menu_id')
        ->toArray();

    // Ambil data menu lengkap berdasarkan urutan ID terlaris di atas
    if (!empty($topMenuIds)) {
        // 🌟 PERBAIKAN: Hanya mengambil menu terlaris yang is_active = true
        $menus = Menu::whereIn('id', $topMenuIds)
            ->where('is_active', true) 
            ->orderByRaw("FIELD(id, " . implode(',', $topMenuIds) . ")")
            ->get();
            
        // SAFETY GUARD: Jika menu terlaris belum sampai 4 macam, penuhi sisa slot otomatis dengan menu terbaru
        if ($menus->count() < 4) {
            $missingCount = 4 - $menus->count();
            $extraMenus = Menu::whereNotIn('id', $topMenuIds)
                ->where('is_active', true)
                ->latest()
                ->take($missingCount)
                ->get();
            $menus = $menus->merge($extraMenus);
        }
    } else {
        // Fallback cadangan jika database benar-benar kosong melompong belum ada transaksi
        $menus = Menu::where('is_active', true)->latest()->take(4)->get();
    }

    // 🌟 PERBAIKAN: Hanya mengirimkan menu per kategori yang statusnya aktif (is_active = true) ke landing page
    $boxMenus = Menu::where('category', 'box')->where('is_active', true)->get();
    $prasmananMenus = Menu::where('category', 'prasmanan')->where('is_active', true)->get();
    $snackMenus = Menu::where('category', 'snack')->where('is_active', true)->get();

    return view('landing', compact('menus', 'boxMenus', 'prasmananMenus', 'snackMenus'));
})->name('landing');

Route::get('/tentang-kami', function () { return view('tentang'); })->name('tentang');

// 🌟 REVISI TAMBAHAN: Rute Baru Halaman Cara Pemesanan Publik
Route::get('/cara-pemesanan', function () { return view('cara_pemesanan'); })->name('cara_pemesanan');

Route::get('/kontak', function () { return view('kontak'); })->name('kontak');

// --- Halaman Review Publik (Data Dinamis) ---
Route::get('/reviews', function () { 
    $allReviews = Review::with(['user', 'menu'])->latest()->get();
    return view('reviews', compact('allReviews')); 
})->name('reviews');


// --- 2. AUTHENTICATION ---
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// --- LUPA PASSWORD ---
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

// --- BUAT PASSWORD BARU ---
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

// Route untuk Google Login
Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('google.login');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');


// --- 3. GOOGLE CALENDAR ROUTES (Proteksi Khusus Admin) ---
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/calendar/connect', [GoogleCalendarController::class, 'redirectToGoogle'])->name('admin.calendar.connect');
    Route::get('/admin/calendar/callback', [GoogleCalendarController::class, 'handleGoogleCallback'])->name('admin.calendar.callback');
});


// --- 4. DASHBOARD ROUTES (Proteksi Auth & Prefix Dashboard) ---
Route::prefix('dashboard')->middleware('auth')->group(function () {

    /**
     * LOGIKA PENGALIHAN DASHBOARD UTAMA
     */
    Route::get('/', function () {
        $role = auth()->user()->role;
        if ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($role === 'owner') {
            return redirect()->route('owner.dashboard');
        }
        return redirect()->route('pelanggan.dashboard');
    })->name('dashboard');


    // --- ROLE: PELANGGAN ---
    Route::middleware('role:pelanggan')->group(function () {
        Route::get('/beranda', [PelangganController::class, 'dashboard'])->name('pelanggan.dashboard');
        
        // 🌟 RUTE BARU: Halaman Riwayat Pesanan
        Route::get('/riwayat', [PelangganController::class, 'riwayat'])->name('pelanggan.riwayat');
        
        // 🛒 MANAJEMEN KERANJANG (Diperbarui agar sinkron dengan file cart.blade.php)
        Route::get('/menu', [PelangganController::class, 'menu'])->name('pelanggan.menu');
        Route::get('/keranjang', [PelangganController::class, 'keranjang'])->name('pelanggan.cart');
        Route::post('/cart/add/{id}', [PelangganController::class, 'addToCart'])->name('cart.add');
        
        // Perbaikan Nama Rute di bawah ini:
        Route::post('/cart/update-quantity/{id}', [PelangganController::class, 'updateQuantity'])->name('pelanggan.cart.updateQuantity');
        Route::delete('/cart/remove/{id}', [PelangganController::class, 'removeItem'])->name('pelanggan.cart.removeItem');
        Route::post('/cart/update-note/{id}', [PelangganController::class, 'updateNote'])->name('pelanggan.cart.updateNote');

        // 💳 PROSES CHECKOUT & STATUS
        Route::post('/checkout/process', [PelangganController::class, 'processCheckout'])->name('pelanggan.checkout.process');
        Route::get('/checkout/status/{id?}', [PelangganController::class, 'checkout'])->name('pelanggan.checkout');

        // ⭐ PROSES REVIEWS
        Route::post('/review/store', [PelangganController::class, 'storeReview'])->name('pelanggan.review.store');
        
        // 🖨️ TAMBAHKAN RUTE NOTA DI SINI
        Route::get('/checkout/nota/{id}', [PelangganController::class, 'cetakNota'])->name('pelanggan.nota');
    });


    // --- ROLE: ADMIN ---
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');

        // Kelola Menu
        Route::get('/kelola-menu', [MenuController::class, 'index'])->name('admin.menu');
        Route::post('/kelola-menu/store', [MenuController::class, 'store'])->name('admin.menu.store');
        
        // 🌟 REVISI DOSEN: Jalur interkoneksi tombol penukar arsip digital menu katering offline
        Route::post('/kelola-menu/{id}/toggle-archive', [MenuController::class, 'toggleArchive'])->name('admin.menu.toggleArchive');
        
        Route::get('/kelola-menu/{id}/edit', [MenuController::class, 'edit'])->name('admin.menu.edit');
        Route::put('/kelola-menu/{id}', [MenuController::class, 'update'])->name('admin.menu.update');
        Route::delete('/kelola-menu/{id}', [MenuController::class, 'destroy'])->name('admin.menu.destroy');

        // Kelola Pesanan
        Route::get('/kelola-pesanan', [OrderController::class, 'index'])->name('admin.orders');
        Route::post('/kelola-pesanan/store-manual', [OrderController::class, 'storeManualOrder'])->name('admin.orders.storeManual'); 
        Route::post('/kelola-pesanan/{id}/status', [OrderController::class, 'updateStatus'])->name('admin.orders.status');
        Route::post('/kelola-pesanan/{id}/complete-payment', [OrderController::class, 'completePayment'])->name('admin.orders.completePayment');

        // ⭐ REVISI FASE 3: MANAJEMEN REVIEW DENGAN BALASAN KOMENTAR
        Route::get('/manajemen-review', [PelangganController::class, 'adminReviewIndex'])->name('admin.reviews.index');
        Route::post('/manajemen-review/reply/{id}', [PelangganController::class, 'updateReviewReply'])->name('admin.reviews.reply');
        Route::delete('/manajemen-review/{id}', [AdminController::class, 'destroyReview'])->name('admin.reviews.destroy');

        // Laporan Penjualan Admin
        Route::get('/laporan-penjualan', [AdminController::class, 'report'])->name('admin.report');

        // 📂 REVISI FASE 2: HALAMAN ARSIP & UPDATE STATUS BARU
        Route::get('/orders/archive', [OrderController::class, 'archive'])->name('admin.orders.archive');
        Route::post('/orders/update-status/{id}', [OrderController::class, 'updateStatus'])->name('admin.orders.updateStatus');

        // 🖨️ REVISI DOSBING: Fitur Cetak Nota Lunas dari Halaman Arsip
        Route::get('/orders/archive/cetak-nota/{id}', [AdminController::class, 'cetakNotaLunas'])->name('admin.cetak_nota');
    });


    // --- ROLE: OWNER ---
    Route::middleware('role:owner')->group(function () {
        Route::get('/owner', [OwnerController::class, 'index'])->name('owner.dashboard');
        Route::get('/owner/laporan', [AdminController::class, 'report'])->name('owner.report');
        Route::get('/owner/reviews', [OwnerController::class, 'allReviews'])->name('owner.reviews');
        
        // 🌟 REVISI UTAMA DOSEN: Memberikan akses mutlak kepada Owner untuk melihat Arsip Penjualan & Cetak Nota asli database
        Route::get('/owner/arsip-penjualan', [OrderController::class, 'archive'])->name('owner.orders.archive');
        Route::get('/owner/arsip-penjualan/cetak-nota/{id}', [AdminController::class, 'cetakNotaLunas'])->name('owner.cetak_nota');
    });

});

// Route untuk menerima laporan pembayaran otomatis dari Midtrans Webhook
Route::post('/midtrans/callback', [PelangganController::class, 'handleNotification']);