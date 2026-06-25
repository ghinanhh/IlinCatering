<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Review; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File; 
use Carbon\Carbon; // 🌟 Tambahan untuk mendeteksi bulan dan tahun otomatis secara real-time

class AdminController extends Controller
{
    public function index()
    {
        // 1. Hitung Total Pendapatan (Status Selesai atau sudah bayar DP/Lunas)
        $totalRevenue = Order::whereNotIn('status', ['pending', 'canceled'])->sum('total_price');

        // 2. 🌟 REVISI DOSEN: Hitung Total Pesanan Keseluruhan
        $totalOrders = Order::count();

        // 🌟 REVISI DOSEN: Hitung Total Pesanan Khusus Bulan Ini Saja
        $totalOrdersBulanIni = Order::whereMonth('created_at', Carbon::now()->month)
                                    ->whereYear('created_at', Carbon::now()->year)
                                    ->count();

        // 3. Ambil 5 Pesanan Terbaru untuk Tabel
        $recentOrders = Order::with('user')->latest()->take(5)->get();

        // 4. Jadwal Masak Terdekat (DIPERBARUI: Pesanan Selesai & Batal Tidak Ditampilkan)
        $upcomingSchedules = Order::where(function($q) {
                                $q->whereIn('status', ['confirmed', 'cooking', 'settlement', 'konfirmasi', 'lunas dp'])
                                  ->orWhere('payment_status', 'settlement');
                            })
                            // 🌟 REVISI UTAMA: Cegah status pesanan selesai, batal, canceled, atau expired masuk ke jadwal dapur
                            ->whereNotIn('status', ['done', 'selesai', 'Selesai', 'DONE', 'batal', 'canceled', 'expired'])
                            ->where('event_date', '>=', now()->subDays(1)->toDateString())
                            ->where('event_date', '<=', now()->addDays(7)->toDateString())
                            ->orderBy('event_date', 'asc')
                            ->orderBy('event_time', 'asc')
                            ->take(10)
                            ->get();

        // 5. Cari Menu Terlaris (Single Value untuk Card Stat Lama)
        $bestSeller = DB::table('order_items')
            ->join('menus', 'order_items.menu_id', '=', 'menus.id')
            ->select('menus.title', DB::raw('SUM(order_items.quantity) as total_qty'))
            ->groupBy('menus.title')
            ->orderBy('total_qty', 'desc')
            ->first();

        // 🌟 REVISI DOSEN: Ambil Data 5 Menu Terlaris Lengkap dengan Jumlah Terjualnya (Strict Mode Secure)
        $menuTerlarisList = DB::table('order_items')
            ->join('menus', 'order_items.menu_id', '=', 'menus.id')
            ->select('menus.title', 'menus.category', 'menus.image', DB::raw('SUM(order_items.quantity) as total_qty'))
            ->groupBy('menus.title', 'menus.category', 'menus.image')
            ->orderBy('total_qty', 'desc')
            ->take(5)
            ->get();

        // 🌟 REVISI DOSEN: Ambil Data 5 Menu Jarang Dibeli (Termasuk yang belum pernah laku / 0 terjual)
        $menuJarangDibeliList = DB::table('menus')
            ->leftJoin('order_items', 'menus.id', '=', 'order_items.menu_id')
            ->select('menus.title', 'menus.category', 'menus.image', DB::raw('IFNULL(SUM(order_items.quantity), 0) as total_qty'))
            ->groupBy('menus.title', 'menus.category', 'menus.image')
            ->orderBy('total_qty', 'asc')
            ->take(5)
            ->get();

        return view('dashboard.admin.index', [
            'totalRevenue' => $totalRevenue,
            'totalOrders' => $totalOrders,
            'totalOrdersBulanIni' => $totalOrdersBulanIni, // Dikirim ke view
            'recentOrders' => $recentOrders,
            'upcomingSchedules' => $upcomingSchedules,
            'bestSeller' => $bestSeller ? $bestSeller->title : 'Belum ada data',
            'menuTerlarisList' => $menuTerlarisList, // Dikirim ke view
            'menuJarangDibeliList' => $menuJarangDibeliList // Dikirim ke view
        ]);
    }

    public function report(Request $request)
    {
        $query = Order::whereNotIn('status', ['pending', 'canceled']);

        if ($request->has('start_date') && $request->start_date != '') {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date != '') {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $orders = $query->latest()->get();
        $totalRevenue = $orders->sum('total_price');

        return view('dashboard.admin.report', compact('orders', 'totalRevenue'));
    }

    public function indexReview()
    {
        $reviews = Review::with(['user', 'menu'])->latest()->get();
        return view('dashboard.admin.review', compact('reviews'));
    }

    public function destroyReview($id)
    {
        $review = Review::findOrFail($id);

        // Hapus file gambar dari storage jika ada sebelum data di database dihapus
        if ($review->image) {
            $imagePath = public_path($review->image);
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
        }

        $review->delete();

        return redirect()->back()->with('success', 'Review pelanggan berhasil dihapus dari sistem!');
    }

    // 🌟 REVISI DOSBING: Fungsi untuk Admin mencetak Nota Lunas dari halaman Arsip
    public function cetakNotaLunas($id)
    {
        // Ambil data pesanan (tanpa membatasi user_id karena ini admin yang akses)
        $order = Order::with(['items.menu', 'user'])->findOrFail($id);

        // Kita daur ulang view 'dashboard.nota' yang sudah kamu buat sebelumnya
        return view('dashboard.nota', compact('order'));
    }
}