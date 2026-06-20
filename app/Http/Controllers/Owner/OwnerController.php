<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OwnerController extends Controller
{
    public function index(Request $request)
    {
        // 🌟 FIX: Kita tampung semua variasi kata selesai agar terbaca sistem
        $statusSukses = ['done', 'selesai', 'Selesai'];

        // 1. Statistik Utama (Keseluruhan)
        $totalOmzet = Order::whereIn('status', $statusSukses)->sum('total_price');
        
        // 🌟 TAMBAHAN REVISI: Omzet khusus bulan berjalan (Dinamis)
        $omzetBulanIni = Order::whereIn('status', $statusSukses)
                              ->whereMonth('created_at', Carbon::now()->month)
                              ->whereYear('created_at', Carbon::now()->year)
                              ->sum('total_price');

        $totalPelanggan = User::where('role', 'pelanggan')->count();
        $pesananSelesai = Order::whereIn('status', $statusSukses)->count();

        // 🌟 REVISI TAMBAHAN DOSEN: Mengambil 10 Jadwal Masak Terdekat (7 Hari ke Depan) untuk Persiapan Belanja Bahan Baku Owner
        $upcomingSchedules = Order::with('items.menu')
                            ->where(function($q) {
                                $q->whereIn('status', ['confirmed', 'cooking', 'settlement', 'konfirmasi', 'lunas dp'])
                                  ->orWhere('payment_status', 'settlement');
                            })
                            ->whereNotIn('status', ['done', 'selesai', 'Selesai', 'DONE'])
                            ->where('event_date', '>=', now()->subDays(1)->toDateString())
                            ->where('event_date', '<=', now()->addDays(7)->toDateString())
                            ->orderBy('event_date', 'asc')
                            ->orderBy('event_time', 'asc')
                            ->take(10)
                            ->get();

        // 2. Data untuk Grafik Penjualan Bulanan (Dengan Filter Tahun)
        $tahunIni = $request->input('year', now()->year);

        $availableYears = Order::select(DB::raw('YEAR(created_at) as year'))
                            ->whereIn('status', $statusSukses)
                            ->groupBy('year')
                            ->orderBy('year', 'desc')
                            ->pluck('year')
                            ->toArray();

        if (!in_array(now()->year, $availableYears)) {
            array_unshift($availableYears, now()->year);
            rsort($availableYears); 
        }

        $penjualanBulanan = Order::select(
            DB::raw('MONTH(created_at) as bulan'),
            DB::raw('SUM(total_price) as total')
        )
        ->whereIn('status', $statusSukses)
        ->whereYear('created_at', $tahunIni)
        ->groupBy('bulan')
        ->get()
        ->keyBy('bulan');

        $grafikBulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $grafikPendapatan = [];

        for ($i = 1; $i <= 12; $i++) {
            $grafikPendapatan[] = isset($penjualanBulanan[$i]) ? (int) $penjualanBulanan[$i]->total : 0;
        }

        // 3. Ambil 5 Review Terbaru
        $recentReviews = [];
        if (class_exists('\App\Models\Review')) {
            $recentReviews = \App\Models\Review::with(['user', 'menu'])
                            ->latest()
                            ->take(5)
                            ->get();
        }

        return view('dashboard.owner.index', compact(
            'totalOmzet', 
            'omzetBulanIni', 
            'totalPelanggan', 
            'pesananSelesai', 
            'grafikBulan', 
            'grafikPendapatan', 
            'recentReviews',
            'tahunIni',
            'availableYears',
            'upcomingSchedules' // Variabel jadwal belanja dikirim ke view owner
        ));
    }

    public function allReviews()
    {
        $reviews = \App\Models\Review::with(['user', 'menu'])->latest()->get();
        return view('dashboard.owner.reviews', compact('reviews'));
    }
}