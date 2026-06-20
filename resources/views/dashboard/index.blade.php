@extends('layouts.app')

@section('content')
<div class="p-6 bg-slate-50 min-h-screen">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-slate-900">
            Halo, {{ Auth::user()->name }}! 👋
        </h1>
        <p class="text-slate-500">Selamat datang kembali di panel Ilin Catering.</p>
    </div>

    {{-- Alert Success --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-500 text-white rounded-2xl shadow-lg shadow-green-100 flex items-center gap-3">
            <i class="fa-solid fa-circle-check"></i>
            <span class="text-xs font-black uppercase tracking-widest">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Statistik Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-200">
            <p class="text-slate-500 text-sm font-bold uppercase tracking-wider">Total Pesanan</p>
            <p class="text-3xl font-black mt-2 text-slate-900">{{ $totalPesanan }}</p>
        </div>
        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-200">
            <p class="text-slate-500 text-sm font-bold uppercase tracking-wider">Pesanan Aktif</p>
            <p class="text-3xl font-black mt-2 text-orange-600">{{ $pesananAktif }}</p>
        </div>
        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-200">
            <p class="text-slate-500 text-sm font-bold uppercase tracking-wider">Review Diberikan</p>
            <p class="text-3xl font-black mt-2 text-green-600">{{ $totalReview }}</p>
        </div>
    </div>

    <div class="flex flex-wrap gap-2 bg-slate-200/50 p-1.5 rounded-2xl mt-10 mb-6 w-fit mx-auto sm:mx-0">
        <button onclick="switchTab('ongoing')" id="btn-ongoing" class="px-6 py-2.5 rounded-xl text-sm font-black transition-all bg-white text-blue-600 shadow-sm flex items-center gap-2">
            <i class="fa-solid fa-spinner animate-spin-slow"></i> Pesanan Berjalan
        </button>
        <button onclick="switchTab('history')" id="btn-history" class="px-6 py-2.5 rounded-xl text-sm font-black transition-all text-slate-500 hover:text-slate-800 flex items-center gap-2">
            <i class="fa-solid fa-clock-rotate-left"></i> Riwayat Selesai
        </button>
    </div>

    {{-- ================= TAB 1: KONTEN PESANAN BERJALAN (ON GOING) ================= --}}
    <div id="tab-ongoing" class="animate-fade-in block">
        <h3 class="text-xl font-bold text-slate-900 mb-6 flex items-center gap-2">
            Pesanan Aktif Sedang Berjalan 🚚
        </h3>

        @php $hasOngoing = false; @endphp
        @foreach($orders as $orderItem)
            @if(!in_array(strtolower($orderItem->status), ['done', 'selesai', 'batal', 'canceled']))
                @php $hasOngoing = true; @endphp
                <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden mb-6 transition-all hover:border-orange-200">
                    <div class="p-5 bg-orange-600 text-white flex justify-between items-center flex-wrap gap-4">
                        <div>
                            <p class="text-[9px] font-bold uppercase tracking-widest opacity-70">Order ID: #{{ $orderItem->order_number }}</p>
                            <h4 class="font-bold text-sm">
                                {{ $orderItem->items->first()->menu->title ?? 'Menu' }} 
                                @if($orderItem->items->count() > 1) 
                                    +{{ $orderItem->items->count() - 1 }} Menu lainnya 
                                @endif
                            </h4>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <a href="{{ url('dashboard/checkout/nota/' . $orderItem->id) }}" class="px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-xl text-[10px] font-black uppercase transition-all flex items-center gap-1.5 border border-white/10">
                                <i class="fa-solid fa-file-invoice"></i> Nota DP
                            </a>
                            
                            <a href="{{ route('pelanggan.checkout', $orderItem->id) }}" class="px-5 py-2 bg-white/20 hover:bg-white/30 backdrop-blur-md rounded-xl text-[10px] font-bold transition flex items-center gap-2 uppercase tracking-widest">
                                Detail <i class="fa-solid fa-chevron-right text-[8px]"></i>
                            </a>
                        </div>
                    </div>
                    
                    <div class="p-8">
                        {{-- Stepper Progress Tracker --}}
                        <div class="flex justify-between items-center relative max-w-2xl mx-auto px-4">
                            
                            @php
                                $statusMap = [
                                    'pending'    => 0, 'lunas dp'   => 0,
                                    'konfirmasi' => 1, 'confirmed'  => 1,
                                    'dimasak'    => 2, 'cooking'    => 2,
                                    'dikirim'    => 3, 'shipping'   => 3,
                                    'selesai'    => 4, 'done'       => 4
                                ];
                                $statusClean = strtolower($orderItem->status);
                                $currentIdx = $statusMap[$statusClean] ?? 0;
                                
                                // 🌟 FIX IKON: Menggunakan jenis ikon universal yang compatible di FontAwesome 5 & 6
                                $icons = ['fa-hourglass-half', 'fa-clipboard-check', 'fa-fire', 'fa-truck', 'fa-flag-checkered'];
                                $progressWidths = ['0%', '25%', '50%', '75%', '100%'];
                                $currentWidth = $progressWidths[$currentIdx] ?? '0%';
                            @endphp

                            <div class="absolute h-1 bg-slate-100 top-5 left-0 right-0 -z-0 rounded-full"></div>
                            <div class="absolute h-1 bg-orange-500 top-5 left-0 -z-0 rounded-full transition-all duration-1000" style="width: {{ $currentWidth }}"></div>
                            
                            @foreach(['Konfirmasi', 'Diterima', 'Dimasak', 'Dikirim', 'Selesai'] as $key => $label)
                            <div class="relative z-10 flex flex-col items-center">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-[10px] transition-all duration-500
                                    @if($key < $currentIdx)
                                        bg-green-500 text-white shadow-lg shadow-green-100
                                    @elseif($key == $currentIdx)
                                        bg-orange-600 text-white shadow-lg shadow-orange-200 ring-4 ring-orange-100 @if($key == 2) animate-bounce @else animate-pulse @endif
                                    @else
                                        bg-slate-100 text-slate-400
                                    @endif
                                ">
                                    {{-- 🌟 FIX WARNA IKON: Mewarnai ikon api menjadi kuning cerah (text-amber-300) jika sedang berstatus aktif dimasak --}}
                                    <i class="fa-solid {{ $key < $currentIdx ? 'fa-check' : $icons[$key] }} {{ $key == $currentIdx && $key == 2 ? 'text-amber-300 text-xs' : '' }}"></i>
                                </div>
                                <p class="text-[9px] font-bold mt-2 uppercase tracking-tighter @if($key <= $currentIdx) text-slate-800 font-black @else text-slate-400 @endif">
                                    {{ $label }}
                                </p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        @endforeach

        @if(!$hasOngoing)
            <div class="bg-white border-2 border-dashed border-slate-200 rounded-[2rem] p-16 text-center">
                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-utensils text-slate-300 text-xl"></i>
                </div>
                <p class="text-slate-500 font-medium italic">Tidak ada pesanan berjalan saat ini.</p>
                <a href="{{ route('pelanggan.menu') }}" class="mt-4 inline-block px-6 py-2 bg-orange-600 text-white rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-orange-700 transition">
                    Pesan Menu Sekarang
                </a>
            </div>
        @endif
    </div>

    {{-- ================= TAB 2: KONTEN RIWAYAT PESANAN SELESAI (ARCHIVE) ================= --}}
    <div id="tab-history" class="animate-fade-in hidden">
        <h3 class="text-xl font-bold text-slate-900 mb-6 flex items-center gap-2">
            Riwayat Transaksi Selesai 📋
        </h3>

        @php $hasHistory = false; @endphp
        @foreach($orders as $orderItem)
            @if(in_array(strtolower($orderItem->status), ['done', 'selesai']))
                @php $hasHistory = true; @endphp
                <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden mb-6 transition-all hover:border-orange-200">
                    <div class="p-5 bg-slate-800 text-white flex justify-between items-center flex-wrap gap-4">
                        <div>
                            <p class="text-[9px] font-bold uppercase tracking-widest opacity-80">Order ID: #{{ $orderItem->order_number }}</p>
                            <h4 class="font-bold text-sm">
                                {{ $orderItem->items->first()->menu->title ?? 'Menu' }} 
                                @if($orderItem->items->count() > 1) 
                                    +{{ $orderItem->items->count() - 1 }} Menu lainnya 
                                @endif
                            </h4>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <a href="{{ route('pelanggan.checkout', $orderItem->id) }}" class="px-4 py-2 bg-yellow-400 hover:bg-yellow-500 text-slate-900 rounded-xl text-[10px] font-black uppercase transition-all shadow-lg flex items-center gap-2 font-bold">
                                Beri Review ⭐
                            </a>
                            <a href="{{ url('dashboard/checkout/nota/' . $orderItem->id) }}" class="px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-xl text-[10px] font-black uppercase transition-all flex items-center gap-1.5 border border-white/10">
                                <i class="fa-solid fa-file-invoice"></i> Nota Lunas
                            </a>
                        </div>
                    </div>
                    
                    <div class="p-8">
                        <div class="text-center py-2">
                            <p class="text-sm text-slate-600 font-medium">
                                <i class="fa-solid fa-circle-check text-green-500 mr-2"></i>
                                Pesanan ini telah selesai pada {{ $orderItem->updated_at->format('d M Y') }}. Terima kasih sudah mempercayai Ilin Catering!
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach

        @if(!$hasHistory)
            <div class="bg-white border-2 border-dashed border-slate-200 rounded-[2rem] p-16 text-center">
                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-box-open text-slate-300 text-xl"></i>
                </div>
                <p class="text-slate-500 font-medium italic">Belum ada riwayat transaksi selesai.</p>
            </div>
        @endif
    </div>
</div>

<style>
    .animate-fade-in { animation: fadeIn 0.3s ease-in-out; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(5px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-spin-slow { animation: spin 3s linear infinite; }
</style>

<script>
    function switchTab(tab) {
        const ongoing = document.getElementById('tab-ongoing');
        const history = document.getElementById('tab-history');
        const btnOngoing = document.getElementById('btn-ongoing');
        const btnHistory = document.getElementById('btn-history');

        if (tab === 'ongoing') {
            ongoing.classList.remove('hidden');
            ongoing.classList.add('block');
            history.classList.add('hidden');
            history.classList.remove('block');
            
            btnOngoing.className = 'px-6 py-2.5 rounded-xl text-sm font-black transition-all bg-white text-blue-600 shadow-sm flex items-center gap-2';
            btnHistory.className = 'px-6 py-2.5 rounded-xl text-sm font-black transition-all text-slate-500 hover:text-slate-800 flex items-center gap-2';
        } else {
            history.classList.remove('hidden');
            history.classList.add('block');
            ongoing.classList.add('hidden');
            ongoing.classList.remove('block');
            
            btnHistory.className = 'px-6 py-2.5 rounded-xl text-sm font-black transition-all bg-white text-orange-600 shadow-sm flex items-center gap-2';
            btnOngoing.className = 'px-6 py-2.5 rounded-xl text-sm font-black transition-all text-slate-500 hover:text-slate-800 flex items-center gap-2';
        }
    }
</script>
@endsection