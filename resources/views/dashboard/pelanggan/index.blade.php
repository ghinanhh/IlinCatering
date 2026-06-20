@extends('layouts.app')

@section('content')
<div class="p-6 bg-slate-50 min-h-screen">

    <div class="mb-8">
        <h2 class="text-3xl font-extrabold text-slate-900">Halo, {{ Auth::user()->name }}! 👋</h2>
        <p class="text-slate-500 mt-2">Selamat datang kembali di panel Ilin Catering.</p>
    </div>

    {{-- Notifikasi Sukses --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-500 text-white rounded-2xl shadow-lg shadow-green-100 flex items-center gap-3">
            <i class="fa-solid fa-circle-check"></i>
            <span class="text-xs font-black uppercase tracking-widest">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Counter Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 transition hover:shadow-md">
            <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest">Total Pesanan</p>
            <h3 class="text-3xl font-black text-slate-900 mt-2">{{ $totalPesanan }}</h3>
        </div>
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 transition hover:shadow-md">
            <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest">Pesanan Aktif</p>
            <h3 class="text-3xl font-black text-blue-600 mt-2">{{ $pesananAktif }}</h3>
        </div>
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 transition hover:shadow-md">
            <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest">Review Diberikan</p>
            <h3 class="text-3xl font-black text-yellow-500 mt-2">{{ $totalReview }}</h3>
        </div>
    </div>

    {{-- ================= SEKSI 1: PESANAN AKTIF (SEDANG BERJALAN) ================= --}}
    <h3 class="text-lg font-black text-slate-900 mb-6 flex items-center gap-2 uppercase tracking-tighter text-blue-600">
        Lacak Pesanan Aktif Kamu 🚚
    </h3>

    <div class="space-y-6 mb-12">
        @php $adaPesananAktif = false; @endphp
        @foreach($orders as $order)
            @if($order->status !== 'done' && $order->status !== 'canceled')
                @php $adaPesananAktif = true; @endphp
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 relative overflow-hidden transition hover:border-blue-200">
                    <div class="bg-blue-600 p-6 text-white flex justify-between items-center">
                        <div>
                            <p class="text-[10px] font-black uppercase opacity-70 tracking-widest">Order ID: #{{ $order->order_number }}</p>
                            <h4 class="text-xl font-black tracking-tighter">
                                @foreach($order->items as $item)
                                    {{ $item->menu->title }}{{ !$loop->last ? ', ' : '' }}
                                @endforeach
                            </h4>
                        </div>
                        <a href="{{ route('pelanggan.checkout', $order->id) }}" class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-xl text-[10px] font-black uppercase transition flex items-center gap-2">
                            Detail Pesanan <i class="fa-solid fa-chevron-right text-[8px]"></i>
                        </a>
                    </div>

                    <div class="p-10">
                        {{-- Progress Tracker --}}
                        <div class="relative flex justify-between items-center mb-6 max-w-4xl mx-auto px-10">
                            <div class="absolute left-0 top-1/2 -translate-y-1/2 w-full h-1 bg-slate-100 rounded-full"></div>
                            <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-green-500 rounded-full transition-all duration-1000" 
                                 style="width: {{ $order->status == 'pending' ? '0%' : ($order->status == 'confirmed' ? '25%' : ($order->status == 'cooking' ? '50%' : ($order->status == 'shipping' ? '75%' : '100%'))) }}"></div>

                            <div class="relative flex flex-col items-center">
                                <div class="w-10 h-10 rounded-full {{ in_array($order->status, ['confirmed','cooking','shipping','done']) ? 'bg-green-500 shadow-lg shadow-green-100' : 'bg-white border-2 border-slate-200' }} flex items-center justify-center relative z-10 transition">
                                    <i class="fa-solid fa-check text-xs {{ in_array($order->status, ['confirmed','cooking','shipping','done']) ? 'text-white' : 'text-slate-300' }}"></i>
                                </div>
                                <p class="absolute -bottom-8 whitespace-nowrap text-[9px] font-black uppercase {{ in_array($order->status, ['confirmed','cooking','shipping','done']) ? 'text-green-600' : 'text-slate-400' }}">Konfirmasi</p>
                            </div>

                            <div class="relative flex flex-col items-center">
                                <div class="w-10 h-10 rounded-full {{ in_array($order->status, ['cooking','shipping','done']) ? 'bg-green-500 shadow-lg shadow-green-100' : 'bg-white border-2 border-slate-200' }} flex items-center justify-center relative z-10 transition">
                                    <i class="fa-solid fa-check text-xs {{ in_array($order->status, ['cooking','shipping','done']) ? 'text-white' : 'text-slate-300' }}"></i>
                                </div>
                                <p class="absolute -bottom-8 whitespace-nowrap text-[9px] font-black uppercase {{ in_array($order->status, ['cooking','shipping','done']) ? 'text-green-600' : 'text-slate-400' }}">Diterima</p>
                            </div>

                            <div class="relative flex flex-col items-center">
                                <div class="w-12 h-12 rounded-full {{ in_array($order->status, ['cooking','shipping','done']) ? 'bg-orange-500 shadow-xl shadow-orange-200' : 'bg-white border-2 border-slate-200' }} flex items-center justify-center relative z-20 transition border-4 border-white">
                                    @if($order->status == 'cooking')
                                        <div class="w-6 h-6 border-4 border-white/30 border-t-white rounded-full animate-spin"></div>
                                    @elseif(in_array($order->status, ['shipping','done']))
                                         <i class="fa-solid fa-check text-xs text-white"></i>
                                    @else
                                        <i class="fa-solid fa-utensils text-xs text-slate-300"></i>
                                    @endif
                                </div>
                                <p class="absolute -bottom-8 whitespace-nowrap text-[9px] font-black uppercase {{ in_array($order->status, ['cooking','shipping','done']) ? 'text-orange-600' : 'text-slate-400' }}">Dimasak</p>
                            </div>

                            <div class="relative flex flex-col items-center">
                                <div class="w-10 h-10 rounded-full {{ in_array($order->status, ['shipping','done']) ? 'bg-blue-500 shadow-lg shadow-blue-100' : 'bg-white border-2 border-slate-200' }} flex items-center justify-center relative z-10 transition">
                                    @if($order->status == 'shipping')
                                        <i class="fa-solid fa-truck-fast fa-bounce text-white text-xs"></i>
                                    @else
                                        <i class="fa-solid fa-truck text-xs {{ $order->status == 'done' ? 'text-white' : 'text-slate-300' }}"></i>
                                    @endif
                                </div>
                                <p class="absolute -bottom-8 whitespace-nowrap text-[9px] font-black uppercase {{ in_array($order->status, ['shipping','done']) ? 'text-blue-600' : 'text-slate-400' }}">Dikirim</p>
                            </div>

                            <div class="relative flex flex-col items-center">
                                <div class="w-10 h-10 rounded-full {{ $order->status == 'done' ? 'bg-slate-900 shadow-lg' : 'bg-white border-2 border-slate-200' }} flex items-center justify-center relative z-10 transition">
                                    <i class="fa-solid fa-flag-checkered text-xs {{ $order->status == 'done' ? 'text-white' : 'text-slate-300' }}"></i>
                                </div>
                                <p class="absolute -bottom-8 whitespace-nowrap text-[9px] font-black uppercase {{ $order->status == 'done' ? 'text-slate-900' : 'text-slate-400' }}">Selesai</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach

        @if(!$adaPesananAktif)
            <div class="bg-white rounded-[2.5rem] p-10 text-center border border-slate-100 text-slate-400 italic text-sm">
                👍 Tidak ada pesanan berjalan saat ini.
            </div>
        @endif
    </div>


    {{-- ================= 🌟 SEKSI 2: REVISI RIWAYAT PESANAN SELESAI (REVIEW PER MENU) ================= --}}
    <h3 class="text-lg font-black text-slate-900 mb-6 flex items-center gap-2 uppercase tracking-tighter text-orange-600">
        Riwayat Pesanan Selesai ⭐
    </h3>

    <div class="space-y-6">
        @php $adaPesananSelesai = false; @endphp
        @foreach($orders as $order)
            @if($order->status == 'done')
                @php $adaPesananSelesai = true; @endphp
                
                <div class="bg-white rounded-[2.5rem] p-6 shadow-sm border border-slate-100 hover:border-orange-200 transition space-y-4">
                    
                    <!-- Header Kartu Nota Selesai -->
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-b border-slate-100 pb-3 gap-2">
                        <div>
                            <span class="text-[9px] font-bold px-2 py-0.5 bg-green-100 text-green-700 rounded-md uppercase tracking-wider">Selesai</span>
                            <p class="text-xs font-bold text-slate-400 mt-1">Nota Transaksi: #{{ $order->order_number }}</p>
                        </div>
                        <div class="sm:text-right">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Total Pembayaran (Lunas)</p>
                            <p class="text-sm font-black text-slate-900">Rp {{ number_format($order->total_price ?? 0, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    
                    <!-- SUB-LOOPING: Pecah review per menu item makanan di dalam orderan ini -->
                    <div class="space-y-3">
                        @foreach($order->items as $item)
                            @php
                                // Cek otomatis ke DB apakah menu ini di orderan ini sudah diulas pelanggan
                                $alreadyReviewed = \App\Models\Review::where('user_id', auth()->id())
                                                                    ->where('menu_id', $item->menu_id)
                                                                    ->where('order_id', $order->id)
                                                                    ->first();
                            @endphp
                            
                            <!-- Wrapper Baris Menu Item bertenaga AlpineJS -->
                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100/70" x-data="{ openForm: false, rating: 5, hoverRating: 0 }">
                                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                                    
                                    <!-- Sisi Kiri: Foto & Judul Hidangan -->
                                    <div class="flex items-center gap-3">
                                        @if($item->menu && $item->menu->image)
                                            <img src="{{ asset('storage/' . $item->menu->image) }}" class="w-12 h-12 rounded-xl object-cover shadow-sm">
                                        @else
                                            <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center text-orange-600 font-bold text-xs">🍱</div>
                                        @endif
                                        <div>
                                            <h4 class="font-bold text-slate-800 text-sm">{{ $item->menu->title ?? 'Menu Telah Dihapus' }}</h4>
                                            <p class="text-xs text-slate-500">{{ $item->quantity }} Porsi x Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                        </div>
                                    </div>

                                    <!-- Sisi Kanan: Kondisi Tombol Kendali -->
                                    <div class="w-full sm:w-auto text-right">
                                        @if($alreadyReviewed)
                                            <div class="inline-flex items-center gap-1 bg-green-50 border border-green-200 px-3 py-1.5 rounded-xl text-green-700 text-xs font-bold shadow-sm">
                                                <i class="fa-solid fa-circle-check text-[10px]"></i> Sudah Diulas (⭐ {{ $alreadyReviewed->rating }})
                                            </div>
                                        @else
                                            <button @click="openForm = !openForm" class="w-full sm:w-auto bg-orange-600 hover:bg-orange-700 text-white text-xs font-black px-4 py-2 rounded-xl transition flex items-center justify-center gap-2 shadow-sm outline-none">
                                                <i class="fa-solid fa-star text-[10px]"></i> Beri Ulasan
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                <!-- ─── AKORDION FORM REVIEW (SLIDE DOWN PER ITEM) ─── -->
                                <div x-show="openForm" x-cloak x-transition class="mt-4 border-t border-slate-200/60 pt-4 space-y-4 text-left">
                                    <form action="{{ route('pelanggan.review.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                        @csrf
                                        <!-- Kirim ID Tunggal (Bukan Array Lagi, Lebih Aman!) -->
                                        <input type="hidden" name="menu_id" value="{{ $item->menu_id }}">
                                        <input type="hidden" name="order_id" value="{{ $order->id }}">

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <!-- Input Rating Bintang Interaktif -->
                                            <div>
                                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Rating Kelezatan Hidangan:</label>
                                                <div class="flex items-center gap-1 text-xl text-slate-200">
                                                    <template x-for="i in 5">
                                                        <button type="button" 
                                                            @click="rating = i"
                                                            @mouseenter="hoverRating = i"
                                                            @mouseleave="hoverRating = 0"
                                                            class="transition focus:outline-none scale-100 hover:scale-125">
                                                            <i class="fa-solid fa-star cursor-pointer"
                                                               :class="(hoverRating ? i <= hoverRating : i <= rating) ? 'text-yellow-400' : 'text-slate-200'"></i>
                                                        </button>
                                                    </template>
                                                    <input type="hidden" name="rating" :value="rating">
                                                </div>
                                            </div>

                                            <!-- Input Upload Bukti Foto Masakan -->
                                            <div>
                                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Foto Masakan Nyata (Opsional):</label>
                                                <input type="file" name="image" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100 cursor-pointer">
                                            </div>
                                        </div>

                                        <!-- Input Catatan Komentar Rasa -->
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Tulis Ulasan Rasa:</label>
                                            <textarea name="comment" rows="2" class="w-full p-3 bg-white border border-slate-200 rounded-xl text-xs outline-none focus:ring-2 focus:ring-orange-500 font-medium text-slate-700" placeholder="Contoh: Ayam bumbu baladonya mantap meresap, porsinya juga banyak banget!" required></textarea>
                                        </div>

                                        <!-- Tombol Aksi Pembatalan / Submit -->
                                        <div class="flex justify-end gap-2 text-xs">
                                            <button type="button" @click="openForm = false" class="bg-slate-200 text-slate-700 px-4 py-2 rounded-xl font-bold hover:bg-slate-300 transition">Batal</button>
                                            <button type="submit" class="bg-slate-900 text-white px-5 py-2 rounded-xl font-bold hover:bg-orange-600 transition shadow-md">Kirim Ulasan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>
            @endif
        @endforeach

        @if(!$adaPesananSelesai)
            <div class="bg-white rounded-[2.5rem] p-12 text-center border border-slate-100 text-slate-400 italic text-sm">
                Belum ada riwayat pesanan selesai untuk diulas.
            </div>
        @endif
    </div>

</div>
@endsection