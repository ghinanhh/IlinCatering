@extends('layouts.app')

@section('content')
<div class="mb-6 sm:mb-8 text-center sm:text-left">
    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Dashboard Owner 👑</h1>
    <p class="text-sm sm:text-base text-slate-500 mt-1 sm:mt-0">Pantau pertumbuhan bisnis Ilin Catering Anda.</p>
</div>

{{-- Statistik Grid Utama --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8 sm:mb-10">
    <div class="bg-slate-900 p-6 sm:p-8 rounded-3xl sm:rounded-[2.5rem] text-white shadow-xl shadow-slate-200">
        <p class="text-slate-400 text-[10px] sm:text-xs font-bold uppercase tracking-widest">Total Omzet Keseluruhan</p>
        <p class="text-2xl sm:text-3xl font-black mt-2 text-orange-500">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</p>
        <p class="text-[9px] sm:text-[10px] text-slate-500 mt-3 sm:mt-4 italic">*Dari total pesanan Selesai</p>
    </div>

    <div class="bg-orange-600 p-6 sm:p-8 rounded-3xl sm:rounded-[2.5rem] text-white shadow-xl shadow-orange-200">
        <p class="text-orange-200 text-[10px] sm:text-xs font-bold uppercase tracking-widest">Pendapatan Bulan Ini</p>
        <p class="text-2xl sm:text-3xl font-black mt-2 text-white">Rp {{ number_format($omzetBulanIni, 0, ',', '.') }}</p>
        <p class="text-[9px] sm:text-[10px] text-orange-200 mt-3 sm:mt-4 italic">*Bulan {{ \Carbon\Carbon::now()->locale('id')->isoFormat('MMMM YYYY') }}</p>
    </div>

    <div class="bg-white p-6 sm:p-8 rounded-3xl sm:rounded-[2.5rem] border border-slate-100 shadow-sm">
        <p class="text-slate-400 text-[10px] sm:text-xs font-bold uppercase tracking-widest">Total Pelanggan</p>
        <p class="text-2xl sm:text-3xl font-black mt-2 text-slate-900">{{ $totalPelanggan }} User</p>
        <p class="text-[9px] sm:text-[10px] text-green-500 mt-3 sm:mt-4 font-bold">↑ Aktif Berlangganan</p>
    </div>

    <div class="bg-white p-6 sm:p-8 rounded-3xl sm:rounded-[2.5rem] border border-slate-100 shadow-sm">
        <p class="text-slate-400 text-[10px] sm:text-xs font-bold uppercase tracking-widest">Pesanan Sukses</p>
        <p class="text-2xl sm:text-3xl font-black mt-2 text-slate-900">{{ $pesananSelesai }} Transaksi</p>
        <p class="text-[9px] sm:text-[10px] text-slate-400 mt-3 sm:mt-4 font-medium uppercase">Ilin Catering Perf.</p>
    </div>
</div>

{{-- 🌟 REVISI RAMPING: Komponen Jadwal Belanja Diperkecil & Berjejer ke Samping (Grid Layout) 🌟 --}}
<div class="bg-slate-900 rounded-3xl p-5 sm:p-6 text-white shadow-xl mb-8 sm:mb-10">
    <div class="mb-4">
        <h3 class="font-black text-sm sm:text-base text-white flex items-center gap-2">
            <i class="fa-solid fa-basket-shopping text-orange-500"></i>
            Jadwal Masak & Manajemen Belanja Pasar 🛒
        </h3>
        <p class="text-[10px] sm:text-xs text-slate-400 mt-0.5">Pantau rincian porsi H-1 s.d H-7 untuk ketepatan pasokan bahan baku katering.</p>
    </div>

    {{-- Mengubah pembungkus menjadi grid 2 kolom di tablet dan 3 kolom di layar laptop --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
        @foreach($upcomingSchedules as $schedule)
            @php
                $tanggalAcara = \Carbon\Carbon::parse($schedule->event_date)->startOfDay();
                $hariIni = \Carbon\Carbon::now()->startOfDay();
                $sisaHari = $hariIni->diffInDays($tanggalAcara, false);
                $isMendekati = ($sisaHari >= 1 && $sisaHari <= 3);
            @endphp

            <div class="border-l-4 {{ $isMendekati ? 'border-orange-500 bg-orange-500/10' : 'border-slate-700 bg-white/5' }} p-3.5 rounded-r-xl relative overflow-hidden flex flex-col justify-between transition-all">
                <div>
                    <div class="flex justify-between items-start gap-2">
                        <div>
                            <p class="text-[10px] {{ $isMendekati ? 'text-orange-400' : 'text-slate-400' }} font-bold uppercase tracking-wider">
                                {{ \Carbon\Carbon::parse($schedule->event_date)->locale('id')->isoFormat('dddd, D MMM YYYY') }}
                            </p>
                            <p class="font-bold text-xs sm:text-sm text-white mt-0.5">
                                ⏰ {{ \Carbon\Carbon::parse($schedule->event_time)->format('H:i') }} WITA
                            </p>
                        </div>
                        @if($isMendekati)
                            <span class="bg-orange-600 text-white text-[8px] font-black uppercase px-2 py-0.5 rounded tracking-wider shrink-0 animate-pulse">
                                H-{{ $sisaHari }} BELANJA
                            </span>
                        @endif
                    </div>

                    {{-- Box Menu Diperkecil Porsinya --}}
                    <div class="mt-3 bg-black/20 border border-white/5 p-2 rounded-lg">
                        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mb-1">Menu Hidangan:</p>
                        <ul class="space-y-0.5 text-[11px] text-slate-200">
                            @foreach($schedule->items as $item)
                                <li class="flex justify-between border-b border-white/5 pb-0.5 last:border-0 last:pb-0">
                                    <span class="truncate pr-2">• {{ $item->menu->title }}</span>
                                    <span class="font-bold text-orange-400 shrink-0">{{ $item->quantity }} Porsi</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <p class="text-[9px] text-slate-500 italic mt-3 pt-2 border-t border-white/5">
                    #{{ $schedule->order_number }} - {{ $schedule->recipient_name }}
                </p>
            </div>
        @endforeach

        @if($upcomingSchedules->isEmpty())
            <div class="col-span-full text-center py-6 border border-dashed border-slate-700 rounded-2xl">
                <i class="fa-solid fa-calendar-xmark text-slate-600 text-xl mb-1 block"></i>
                <p class="text-slate-500 italic text-xs">Belum ada jadwal masak terdekat. Operasional belanja pasar sedang santai.</p>
            </div>
        @endif
    </div>
</div>

{{-- Baris Analisis Grafik / Tabel Pendapatan --}}
<div class="bg-white p-6 sm:p-10 rounded-3xl sm:rounded-[3rem] border border-slate-100 shadow-sm overflow-hidden mb-8 sm:mb-10">
    <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center mb-6 gap-4 border-b border-slate-100 pb-6">
        <div>
            <h3 class="font-black text-sm sm:text-base text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-chart-pie text-orange-600"></i>
                Analisis Pendapatan ({{ $tahunIni }})
            </h3>
            <p class="text-[10px] sm:text-xs text-slate-500 font-medium mt-1">Lihat rekapitulasi performa katering secara visual atau angka.</p>
        </div>

        <div class="flex flex-col sm:flex-row items-center gap-4 w-full xl:w-auto">
            <div class="flex bg-slate-100 p-1.5 rounded-xl w-full sm:w-auto justify-center">
                <button onclick="switchView('chart')" id="btn-chart" class="px-5 py-2 rounded-lg text-xs font-bold transition-all bg-white text-orange-600 shadow-sm flex items-center gap-2">
                    <i class="fa-solid fa-chart-column"></i> Grafik
                </button>
                <button onclick="switchView('table')" id="btn-table" class="px-5 py-2 rounded-lg text-xs font-bold transition-all text-slate-500 hover:text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-table"></i> Tabel Detail
                </button>
            </div>

            <form action="{{ route('owner.dashboard') }}" method="GET" class="flex items-center gap-3 bg-slate-50 border border-slate-200 px-3 py-1.5 rounded-xl shadow-sm w-full sm:w-auto">
                <label for="year" class="text-[10px] font-black text-slate-500 uppercase tracking-widest hidden sm:block">Tahun:</label>
                <select name="year" id="year" onchange="this.form.submit()" class="bg-transparent text-slate-800 text-sm font-black outline-none cursor-pointer w-full">
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}" {{ $tahunIni == $year ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>
    
    <div id="view-chart" class="relative h-72 sm:h-80 w-full animate-fade-in block">
        <canvas id="grafikPenjualanBatang"></canvas>
    </div>

    <div id="view-table" class="animate-fade-in hidden overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[500px]">
            <thead class="bg-slate-50 border-b-2 border-slate-100">
                <tr>
                    <th class="p-4 sm:p-5 text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-widest w-1/4">Bulan</th>
                    <th class="p-4 sm:p-5 text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-widest text-right">Total Pendapatan</th>
                    <th class="p-4 sm:p-5 text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-widest text-center w-1/4">Status Pencapaian</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 text-sm">
                @php
                    $namaBulanLengkap = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                    $maxPendapatan = count($grafikPendapatan) > 0 ? max($grafikPendapatan) : 0;
                @endphp

                @foreach($grafikPendapatan as $index => $pendapatan)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="p-4 sm:p-5 font-bold text-slate-700">
                            {{ $namaBulanLengkap[$index] }}
                        </td>
                        <td class="p-4 sm:p-5 font-black text-slate-900 text-right">
                            @if($pendapatan > 0)
                                Rp {{ number_format($pendapatan, 0, ',', '.') }}
                            @else
                                <span class="text-slate-400 font-medium">Rp 0</span>
                            @endif
                        </td>
                        <td class="p-4 sm:p-5 text-center">
                            @if($pendapatan > 0 && $pendapatan == $maxPendapatan)
                                <span class="bg-orange-100 text-orange-700 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider flex items-center justify-center gap-1.5 mx-auto w-fit">
                                    <i class="fa-solid fa-trophy text-orange-500"></i> Tertinggi
                                </span>
                            @elseif($pendapatan > 0)
                                <span class="bg-green-50 text-green-600 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider">
                                    Ada Pemasukan
                                </span>
                            @else
                                <span class="text-slate-400 text-[10px] font-medium italic">
                                    N/A
                                </span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-slate-900 text-white">
                <tr>
                    <td class="p-4 sm:p-5 font-black uppercase tracking-widest text-xs sm:text-sm">TOTAL KESELURUHAN ({{ $tahunIni }})</td>
                    <td class="p-4 sm:p-5 font-black text-orange-400 text-right text-base sm:text-lg">
                        Rp {{ number_format(array_sum($grafikPendapatan), 0, ',', '.') }}
                    </td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

{{-- Baris Review Pelanggan --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8">
    <div class="lg:col-span-2 bg-white p-6 sm:p-8 rounded-3xl sm:rounded-[3rem] border border-slate-100 shadow-sm">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-3">
            <h3 class="font-black text-sm sm:text-base text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-star text-yellow-400"></i>
                Suara Pelanggan Terbaru 💬
            </h3>
        </div>
        
        <div class="space-y-4">
            @forelse($recentReviews ?? [] as $review)
            <div class="p-4 sm:p-5 bg-slate-50 rounded-2xl sm:rounded-3xl border border-slate-100 transition hover:bg-white hover:shadow-md group">
                <div class="flex flex-col sm:flex-row justify-between items-start mb-2 gap-2 sm:gap-0">
                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-orange-100 text-orange-600 rounded-xl sm:rounded-2xl flex items-center justify-center text-[10px] sm:text-xs font-black shrink-0">
                            {{ strtoupper(substr($review->user->name, 0, 1)) }}
                        </div>
                        <div class="flex-1">
                            <p class="text-xs sm:text-sm font-black text-slate-900 line-clamp-1">{{ $review->user->name }}</p>
                            <p class="text-[9px] sm:text-[10px] text-slate-400 font-bold uppercase tracking-tighter italic line-clamp-1">Menu: {{ $review->menu->title }}</p>
                        </div>
                        
                        <div class="flex sm:hidden text-yellow-400 text-[9px] gap-0.5 ml-auto">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fa-solid fa-star {{ $i <= $review->rating ? '' : 'text-slate-200' }}"></i>
                            @endfor
                        </div>
                    </div>
                    
                    <div class="hidden sm:flex text-yellow-400 text-[10px] gap-0.5 shrink-0">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fa-solid fa-star {{ $i <= $review->rating ? '' : 'text-slate-200' }}"></i>
                        @endfor
                    </div>
                </div>
                
                <p class="text-[11px] sm:text-xs text-slate-600 italic leading-relaxed mt-3 sm:mt-1 ml-0 sm:ml-[52px]">"{{ $review->comment }}"</p>
            </div>
            @empty
            <div class="text-center py-8 sm:py-10 bg-slate-50 rounded-2xl sm:rounded-3xl border border-dashed border-slate-200">
                <i class="fa-solid fa-comment-slash text-slate-300 text-2xl sm:text-3xl mb-2 sm:mb-3"></i>
                <p class="text-slate-400 italic text-xs sm:text-sm">Belum ada review masuk dari pelanggan.</p>
            </div>
            @endforelse
        </div>
    </div>

    <div class="bg-gradient-to-br from-orange-500 to-orange-700 p-6 sm:p-8 rounded-3xl sm:rounded-[3rem] text-white shadow-xl shadow-orange-100 flex flex-col justify-center text-center relative overflow-hidden">
        <i class="fa-solid fa-utensils absolute -bottom-10 -right-10 text-8xl sm:text-9xl opacity-10 rotate-12"></i>
        
        <div class="relative z-10">
            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-white/20 backdrop-blur-md rounded-2xl sm:rounded-3xl flex items-center justify-center mx-auto mb-4 sm:mb-6 shadow-lg">
                <i class="fa-solid fa-trophy text-3xl sm:text-4xl text-yellow-300"></i>
            </div>
            <h4 class="font-black text-xl sm:text-2xl mb-2 sm:mb-3 uppercase tracking-tighter">Kepuasan Pelanggan</h4>
            <p class="text-xs sm:text-sm text-orange-100 leading-relaxed mb-4 sm:mb-6 px-2 sm:px-0">
                Every rating from our customers proves that the taste and quality of Ilin Catering remains exceptional.
            </p>
            <div class="bg-white/10 py-2 sm:py-3 px-3 sm:px-4 rounded-xl sm:rounded-2xl border border-white/20 inline-block w-full">
                <p class="text-[9px] sm:text-[10px] font-bold uppercase tracking-widest text-orange-100">Status Bisnis:</p>
                <p class="text-base sm:text-lg font-black text-white uppercase tracking-widest mt-1">Sangat Baik</p>
            </div>
        </div>
    </div>
</div>

<style>
    .animate-fade-in {
        animation: fadeIn 0.3s ease-in-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(5px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function switchView(view) {
        const chartView = document.getElementById('view-chart');
        const tableView = document.getElementById('view-table');
        const btnChart = document.getElementById('btn-chart');
        const btnTable = document.getElementById('btn-table');

        if (view === 'chart') {
            chartView.classList.remove('hidden');
            chartView.classList.add('block');
            tableView.classList.add('hidden');
            tableView.classList.remove('block');
            
            btnChart.className = 'px-5 py-2 rounded-lg text-xs font-bold transition-all bg-white text-orange-600 shadow-sm flex items-center gap-2';
            btnTable.className = 'px-5 py-2 rounded-lg text-xs font-bold transition-all text-slate-500 hover:text-slate-800 flex items-center gap-2';
        } else {
            tableView.classList.remove('hidden');
            tableView.classList.add('block');
            chartView.classList.add('hidden');
            chartView.classList.remove('block');
            
            btnTable.className = 'px-5 py-2 rounded-lg text-xs font-bold transition-all bg-white text-orange-600 shadow-sm flex items-center gap-2';
            btnChart.className = 'px-5 py-2 rounded-lg text-xs font-bold transition-all text-slate-500 hover:text-slate-800 flex items-center gap-2';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('grafikPenjualanBatang').getContext('2d');
        const labelBulan = @json($grafikBulan);
        const dataPendapatan = @json($grafikPendapatan);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labelBulan,
                datasets: [{
                    label: 'Total Pendapatan (Rp)',
                    data: dataPendapatan,
                    backgroundColor: '#ea580c',
                    hoverBackgroundColor: '#c2410c',
                    borderRadius: 6,
                    barThickness: window.innerWidth < 640 ? 15 : 35
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                let nilai = context.raw || 0;
                                return ' Pendapatan: Rp ' + nilai.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9', drawBorder: false },
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000000) return 'Rp ' + (value / 1000000) + ' Jt';
                                if (value >= 1000) return 'Rp ' + (value / 1000) + ' Rb';
                                return 'Rp ' + value;
                            },
                            font: { size: 11, family: "'Nunito', sans-serif" }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { size: 12, weight: 'bold', family: "'Nunito', sans-serif" },
                            color: '#64748b'
                        }
                    }
                }
            }
        });
    });
</script>
@endsection