@extends('layouts.app')

@section('content')
{{-- 🌟 CDN KALENDER FLATPICKR UNTUK VISUALISASI DASHBOARD ADMIN --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<div class="p-6 bg-slate-50 min-h-screen">

    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-slate-900">Dashboard Utama 📊</h1>
        <p class="text-slate-500">Ringkasan performa Ilin Catering hari ini.</p>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-sm font-semibold flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-500"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-sm font-semibold flex items-center gap-2">
            <i class="fa-solid fa-circle-exclamation text-rose-500"></i>
            {{ session('error') }}
        </div>
    @endif

    <div class="mb-8 bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-calendar-days"></i>
            </div>
            <div>
                <h3 class="font-bold text-slate-900 text-base">Sistem Integrasi Google Calendar</h3>
                <p class="text-slate-500 text-xs mt-0.5">Otomatisasi jadwal masak katering langsung ke Google Calendar setelah pelanggan membayar.</p>
            </div>
        </div>
        <div class="w-full md:w-auto text-right">
            @if(auth()->user()->google_calendar_token)
                <button class="w-full md:w-auto bg-emerald-100 text-emerald-700 px-5 py-2.5 rounded-2xl text-xs font-black uppercase tracking-wider flex items-center justify-center gap-2 cursor-not-allowed" disabled>
                    <i class="fa-solid fa-check-double"></i> Terhubung
                </button>
            @else
                <a href="{{ route('admin.calendar.connect') }}" class="w-full md:w-auto bg-orange-600 hover:bg-orange-700 text-white px-5 py-2.5 rounded-2xl text-xs font-black uppercase tracking-wider inline-flex items-center justify-center gap-2 shadow-sm shadow-orange-100 transition-all">
                    <i class="fa-brands fa-google"></i> Hubungkan Kalender
                </a>
            @endif
        </div>
    </div>

    {{-- Statistik Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-10">
        <div class="bg-white p-5 rounded-[2rem] shadow-sm border border-slate-100">
            <div class="w-10 h-10 bg-orange-100 text-orange-600 rounded-2xl flex items-center justify-center mb-3 text-base">
                <i class="fa-solid fa-money-bill-wave"></i>
            </div>
            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">Total Pendapatan</p>
            <p class="text-xl font-black mt-1 text-slate-900 truncate">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
        </div>

        <div class="bg-white p-5 rounded-[2rem] shadow-sm border border-slate-100">
            <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center mb-3 text-base">
                <i class="fa-solid fa-cart-shopping"></i>
            </div>
            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">Total Pesanan</p>
            <p class="text-xl font-black mt-1 text-slate-900">{{ $totalOrders }}</p>
        </div>

        <div class="bg-white p-5 rounded-[2rem] shadow-sm border border-slate-100">
            <div class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center mb-3 text-base">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">Bulan Ini ({{ \Carbon\Carbon::now()->locale('id')->translatedFormat('F') }})</p>
            <p class="text-xl font-black mt-1 text-emerald-600">{{ $totalOrdersBulanIni }}</p>
        </div>

        <div class="bg-white p-5 rounded-[2rem] shadow-sm border border-slate-100">
            <div class="w-10 h-10 bg-purple-100 text-purple-600 rounded-2xl flex items-center justify-center mb-3 text-base">
                <i class="fa-solid fa-utensils"></i>
            </div>
            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">Menu Terlaris</p>
            <p class="text-sm font-black mt-2 text-slate-900 truncate" title="{{ $bestSeller }}">{{ $bestSeller ?? '-' }}</p>
        </div>

        <div class="bg-white p-5 rounded-[2rem] shadow-sm border border-slate-100">
            <div class="w-10 h-10 bg-green-100 text-green-600 rounded-2xl flex items-center justify-center mb-3 text-base">
                <i class="fa-solid fa-wallet"></i>
            </div>
            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">Target DP (30%)\</p>
            <p class="text-xl font-black mt-1 text-green-600 truncate">Rp {{ number_format($totalRevenue * 0.3, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Main Row Tengah (3 Kolom Sejajar) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        
        {{-- KOLOM 1: PESANAN TERBARU --}}
        <div class="bg-white rounded-[2.5rem] p-6 border border-slate-100 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-black text-slate-900 text-base">Pesanan Terbaru</h3>
                    <a href="{{ route('admin.orders') }}" class="text-xs font-bold text-orange-600 hover:underline">Lihat Semua</a>
                </div>
                <div class="space-y-4">
                    @foreach($recentOrders as $order)
                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 bg-white rounded-full flex items-center justify-center text-xs font-bold text-slate-400">
                                #
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-900">{{ $order->user->name ?? 'Pelanggan Offline' }}</p>
                                <p class="text-[10px] text-slate-500">{{ $order->created_at->locale('id')->diffForHumans() }}</p>
                            </div>
                        </div>
                        
                        @php 
                            $statusName = strtolower($order->status); 
                            $statusDisplay = $order->status;

                            if (in_array($statusName, ['done', 'selesai'])) $statusDisplay = 'Selesai';
                            elseif (in_array($statusName, ['canceled', 'batal'])) $statusDisplay = 'Batal';
                            elseif (in_array($statusName, ['pending'])) $statusDisplay = 'Pending';
                            elseif (in_array($statusName, ['lunas dp'])) $statusDisplay = 'Lunas DP';
                            elseif (in_array($statusName, ['confirmed', 'konfirmasi'])) $statusDisplay = 'Konfirmasi';
                            elseif (in_array($statusName, ['cooking', 'dimasak'])) $statusDisplay = 'Dimasak';
                            elseif (in_array($statusName, ['shipping', 'dikirim'])) $statusDisplay = 'Dikirim';
                        @endphp
                        
                        <div class="flex flex-col items-end gap-1">
                            @if($statusName === 'pending')
                                <span class="px-2.5 py-0.5 rounded-full text-[9px] font-black bg-orange-100 text-orange-600 uppercase">
                                    {{ $statusDisplay }}
                                </span>
                            @elseif(in_array($statusName, ['confirmed', 'cooking', 'lunas dp', 'konfirmasi', 'dimasak', 'shipping', 'dikirim']))
                                <span class="px-2.5 py-0.5 rounded-full text-[9px] font-black bg-blue-100 text-blue-600 uppercase">
                                    {{ $statusDisplay }}
                                </span>
                            @elseif(in_array($statusName, ['done', 'selesai']))
                                <span class="px-2.5 py-0.5 rounded-full text-[9px] font-black bg-emerald-100 text-emerald-600 uppercase">
                                    {{ $statusDisplay }}
                                </span>
                            @elseif(in_array($statusName, ['canceled', 'batal']))
                                <span class="px-2.5 py-0.5 rounded-full text-[9px] font-black bg-rose-100 text-rose-600 uppercase">
                                    {{ $statusDisplay }}
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[9px] font-black bg-slate-100 text-slate-600 uppercase">
                                    {{ $statusDisplay }}
                                </span>
                            @endif

                            @if(in_array($statusName, ['confirmed', 'cooking', 'lunas dp', 'konfirmasi', 'dimasak', 'shipping', 'dikirim']))
                                <a href="https://wa.me/?text=Halo%20Kurir%20Ilin%20Catering,%20mohon%20klik%20tautan%20ini%20untuk%20validasi%20jika%20hantaran%20sudah%20sampai%20dan%20COD%20lunas%20di%20lokasi%20pelanggan:%20{{ route('kurir.validasi', $order->order_number) }}" 
                                   target="_blank" 
                                   class="inline-flex items-center gap-1 bg-green-600 hover:bg-green-700 text-white text-[8px] font-black uppercase px-2 py-0.5 rounded transition whitespace-nowrap">
                                    <i class="fa-brands fa-whatsapp"></i> Link Kurir
                                </a>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- KOLOM 2: KALENDER PEMANTAU JADWAL BULANAN ADMIN --}}
        <div class="bg-white rounded-[2.5rem] p-6 border border-slate-100 shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="font-black text-slate-900 text-base mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-calendar text-orange-600"></i>
                    Kalender Pemantau Kuota
                </h3>
                {{-- 🌟 FIX TAMPILAN: Container luar menggunakan overflow-x-auto, isi dalam flex-col agar pas sempurna --}}
                <div class="w-full overflow-x-auto bg-slate-50 p-4 rounded-2xl border border-slate-200 flex flex-col items-center">
                    <input type="text" id="admin_dashboard_calendar" class="hidden">
                </div>
                <div class="mt-4 flex items-center gap-2 text-[9px] font-black uppercase tracking-wider text-orange-700 px-1">
                    <span class="w-2.5 h-2.5 bg-orange-100 border border-orange-300 rounded-md inline-block"></span> Tanggal Terisi Pesanan / Google Agenda
                </div>
            </div>
        </div>

        {{-- KOLOM 3: JADWAL MASAK TERDEKAT --}}
        <div class="bg-slate-900 rounded-[2.5rem] p-6 text-white shadow-xl shadow-slate-200 flex flex-col justify-between">
            <div>
                <h3 class="font-black text-base mb-6 flex items-center gap-2 shrink-0">
                    <i class="fa-solid fa-calendar-day text-orange-500"></i>
                    Jadwal Masak Terdekat
                </h3>
                
                <div class="space-y-4 max-h-[300px] overflow-y-auto pr-2 custom-scrollbar-dark">
                    @foreach($upcomingSchedules as $schedule)
                        @php
                            $tanggalAcara = \Carbon\Carbon::parse($schedule->event_date)->startOfDay();
                            $hariIni = \Carbon\Carbon::now()->startOfDay();
                            $sisaHari = $hariIni->diffInDays($tanggalAcara, false);
                            $isMendekati = ($sisaHari >= 1 && $sisaHari <= 3);
                        @endphp

                        <div class="border-l-4 {{ $isMendekati ? 'border-orange-500 bg-orange-500/10' : 'border-slate-700' }} pl-3 py-1.5 rounded-r-xl transition-all relative overflow-hidden">
                            <div class="flex justify-between items-start relative z-10">
                                <div>
                                    <p class="text-[10px] {{ $isMendekati ? 'text-orange-400' : 'text-slate-400' }} font-bold uppercase tracking-widest">{{ \Carbon\Carbon::parse($schedule->event_date)->locale('id')->isoFormat('dddd, D MMM YYYY') }}</p>
                                    <p class="font-bold text-base text-white mt-0.5">{{ \Carbon\Carbon::parse($schedule->event_time)->format('H:i') }} WITA</p>
                                </div>
                                
                                @if($isMendekati)
                                    <span class="bg-orange-600 text-white text-[8px] font-black uppercase px-2 py-0.5 rounded tracking-wider animate-pulse shadow-[0_0_10px_rgba(234,88,12,0.6)]">
                                        ⚠️ H-{{ $sisaHari }}
                                    </span>
                                @endif
                            </div>
                            
                            <p class="text-xs text-slate-400 italic mt-1 relative z-10 truncate">#{{ $schedule->order_number }} - {{ $schedule->recipient_name }}</p>
                        </div>
                    @endforeach

                    @if($upcomingSchedules->isEmpty())
                        <div class="text-center py-6 border border-dashed border-slate-700 rounded-2xl">
                            <i class="fa-solid fa-mug-hot text-slate-600 text-xl mb-2 block"></i>
                            <p class="text-slate-500 italic text-xs">Dapur sedang santai. Belum ada jadwal.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- LEADERBOARD --}}
    <div class="grid grid-cols-1 gap-4 mt-8">
        <div class="bg-white rounded-[2.5rem] p-6 sm:p-8 shadow-sm border border-slate-100">
            <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 border-b border-slate-100 pb-4">
                <div>
                    <h3 class="text-base font-black text-slate-950 flex items-center gap-2 uppercase tracking-tight">
                        <span class="flex h-2 w-2 relative">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        Peringkat Performa Menu
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Analisis urutan performa porsi penjualan seluruh menu hidangan katering.</p>
                </div>
                <span class="text-[10px] font-black uppercase bg-slate-100 text-slate-500 px-3 py-1 rounded-xl tracking-wider shrink-0">
                    Urutan Menurun (Best Performance)
                </span>
            </div>

            @php
                $combinedMenus = collect($menuTerlarisList)
                    ->merge($menuJarangDibeliList)
                    ->unique('title')
                    ->sortByDesc('total_qty');
            @endphp

            <div class="flex flex-col gap-3 max-h-[480px] overflow-y-auto pr-2 custom-scrollbar">
                @forelse($combinedMenus as $index => $menu)
                    @php $rank = $loop->iteration; @endphp
                    
                    <div class="flex items-center justify-between p-4 rounded-2xl border transition-all duration-300
                        @if($rank <= 3)
                            bg-emerald-50/50 border-emerald-100 hover:border-emerald-200
                        @elseif($rank <= 5)
                            bg-orange-50/50 border-orange-100 hover:border-orange-200
                        @else
                            bg-white border-slate-100 hover:border-slate-200 hover:shadow-xs
                        @endif">
                        
                        <div class="flex items-center gap-4">
                            <span class="w-8 text-center text-xs font-black tracking-tighter shrink-0
                                @if($rank <= 3) text-emerald-600 text-sm scale-110 @elseif($rank <= 5) text-orange-600 text-sm scale-110 @else text-slate-400 font-semibold @endif">
                                #{{ $rank }}
                            </span>
                            
                            <div class="shrink-0 relative">
                                @if($menu->image)
                                    <img src="{{ asset('storage/' . $menu->image) }}" class="w-11 h-11 rounded-xl object-cover border border-white shadow-xs">
                                @else
                                    <div class="w-11 h-11 bg-slate-100 rounded-xl flex items-center justify-center text-xs shadow-xs">🍱</div>
                                @endif
                            </div>

                            <div>
                                <h4 class="font-bold text-xs sm:text-sm text-slate-800 capitalize flex items-center gap-2">
                                    {{ $menu->title }}
                                    @if($rank == 1 && $menu->total_qty > 0)
                                        <i class="fa-solid fa-trophy text-yellow-500 text-xs animate-bounce"></i>
                                    @elseif($rank <= 3 && $menu->total_qty > 0)
                                        <i class="fa-solid fa-arrow-trend-up text-emerald-500 text-xs"></i>
                                    @elseif($rank <= 5 && $menu->total_qty > 0)
                                        <i class="fa-solid fa-fire text-orange-500 text-xs"></i>
                                    @endif
                                </h4>
                                <p class="text-[10px] text-slate-400 font-medium capitalize mt-0.5">{{ $menu->category }}</p>
                            </div>
                        </div>

                        <div class="shrink-0">
                            @if($rank <= 3)
                                <span class="inline-flex items-center gap-1 bg-emerald-500 text-white text-[11px] font-black px-3.5 py-1.5 rounded-xl shadow-xs whitespace-nowrap">
                                    📈 {{ $menu->total_qty }} Porsi
                                </span>
                            @elseif($rank <= 5)
                                <span class="inline-flex items-center gap-1 bg-orange-500 text-white text-[11px] font-black px-3.5 py-1.5 rounded-xl shadow-xs whitespace-nowrap">
                                    🔥 {{ $menu->total_qty }} Porsi
                                </span>
                            @else
                                <span class="inline-flex items-center bg-slate-100 text-slate-500 text-[11px] font-bold px-3 py-1.5 rounded-xl whitespace-nowrap">
                                    {{ $menu->total_qty }} Porsi
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-400 italic text-xs">Belum ada data menu hidangan yang terekam.</div>
                @endforelse
            </div>
        </div>
    </div>

</div>

{{-- 🌟 SCRIPT INTERAKTIF DASHBOARD KALENDER SINKRONISASI TOTAL (LOCAL DB + GOOGLE API) --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const localDates = @json(collect($upcomingSchedules)->pluck('event_date')->map(fn($d) => date('Y-m-d', strtotime($d)))->toArray());
        const googleDates = @json($googleDates ?? []);
        const orderDates = [...new Set([...localDates, ...googleDates])];

        flatpickr("#admin_dashboard_calendar", {
            inline: true,
            dateFormat: "Y-m-d",
            locale: { firstDayOfWeek: 1 },
            onDayCreate: function(dObj, dStr, fp, dayElem) {
                const localDateStr = dayElem.dateObj.getFullYear() + '-' +
                    String(dayElem.dateObj.getMonth() + 1).padStart(2, '0') + '-' +
                    String(dayElem.dateObj.getDate()).padStart(2, '0');

                if (orderDates.includes(localDateStr)) {
                    dayElem.classList.add("admin-has-order");
                }
            }
        });
    });
</script>

<style>
    /* 🌟 FIX CSS UTAMA: Kalender dipaksa di lebar asli tanpa memotong kolom */
    .flatpickr-calendar {
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        width: 308px !important; /* Kunci ukuran grid dasar flatpickr */
        min-width: 308px !important;
    }
    .flatpickr-months .flatpickr-month, .flatpickr-current-month .flatpickr-monthDropdown-months {
        font-weight: 800 !important;
        color: #0f172a !important;
    }
    .flatpickr-day.admin-has-order {
        background: #ffedd5 !important;
        color: #ea580c !important;
        border-radius: 12px !important;
        font-weight: 900 !important;
        border: 1px solid #fed7aa !important;
    }
    .flatpickr-day.admin-has-order:hover {
        background: #fed7aa !important;
    }
    
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }

    .custom-scrollbar-dark::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar-dark::-webkit-scrollbar-track { background: #0f172a; }
    .custom-scrollbar-dark::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }
    .custom-scrollbar-dark::-webkit-scrollbar-thumb:hover { background: #475569; }
</style>
@endsection