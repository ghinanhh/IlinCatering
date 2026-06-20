@extends('layouts.app')

@section('content')
<div class="p-4 sm:p-6 bg-slate-50 min-h-screen">
    
    <div class="mb-6 sm:mb-8">
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Status Pesanan Saya 📋</h2>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">Pantau proses masak dan pengiriman pesananmu di sini.</p>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-500 text-white rounded-2xl shadow-lg shadow-green-100 flex items-center gap-3 text-xs font-bold uppercase">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- ─── BANNER CONTAINER UTAMA ─── --}}
    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden mb-8">
        
        <!-- Header Banner Orange -->
        <div class="p-5 sm:p-6 bg-orange-600 text-white flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <p class="text-[9px] font-black uppercase tracking-widest opacity-80">ID PESANAN: #{{ $order->order_number }}</p>
                <h4 class="font-bold text-sm sm:text-base mt-0.5">
                    @foreach($order->items as $item)
                        {{ $item->menu->title ?? 'Menu' }} ({{ $item->quantity }}x){{ !$loop->last ? ', ' : '' }}
                    @endforeach
                </h4>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <span class="bg-white/20 backdrop-blur-md px-3 py-1 rounded-full text-[9px] font-black uppercase">
                    Sistem: {{ $order->status }}
                </span>
                <span class="bg-white/20 backdrop-blur-md px-3 py-1 rounded-full text-[9px] font-black uppercase">
                    DP: {{ $order->payment_status }}
                </span>
            </div>
        </div>

        <!-- Rincian Biaya Grid -->
        <div class="p-6 sm:p-8 grid grid-cols-1 sm:grid-cols-3 gap-6 border-b border-slate-100 bg-slate-50/50">
            <div>
                <p class="text-slate-400 text-[10px] font-black uppercase tracking-wider">Total Tagihan</p>
                <p class="text-lg sm:text-xl font-black text-slate-900 mt-1">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-slate-400 text-[10px] font-black uppercase tracking-wider">DP 30% (Wajib Bayar)</p>
                <p class="text-lg sm:text-xl font-black text-green-600 mt-1">Rp {{ number_format($order->dp_amount, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-slate-400 text-[10px] font-black uppercase tracking-wider">Sisa Pelunasan (COD)</p>
                <p class="text-lg sm:text-xl font-black text-orange-600 mt-1 {{ $order->remaining_payment == 0 ? 'line-through text-slate-300' : '' }}">
                    Rp {{ number_format($order->remaining_payment, 0, ',', '.') }}
                </p>
            </div>
        </div>

        <!-- 🌟 REVISI UX: Kotak Integrasi Pembayaran Midtrans (Dipertegas) -->
        @if($order->payment_status !== 'settlement' && !in_array(strtolower($order->status), ['done', 'selesai']))
        <div class="p-6 sm:p-8 bg-gradient-to-r from-orange-50 to-amber-50 border-b-2 border-orange-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 relative overflow-hidden">
            <!-- Elemen Dekoratif UI -->
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-orange-200 rounded-full blur-3xl opacity-50 pointer-events-none"></div>
            
            <div class="max-w-xl relative z-10">
                <div class="flex items-center gap-2 mb-2">
                    <span class="flex h-3 w-3 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-orange-500"></span>
                    </span>
                    <h5 class="font-black text-orange-900 text-base sm:text-lg uppercase tracking-tight">Tindakan Diperlukan: Bayar DP 30%</h5>
                </div>
                <p class="text-xs text-orange-800/80 font-medium leading-relaxed">
                    Pesanan Anda sudah masuk, namun <strong>dapur belum bisa memproses masakan Anda</strong>. Silakan selesaikan pembayaran Uang Muka (DP) agar jadwal pesanan Anda segera dikunci oleh sistem.
                </p>
            </div>
            
            <div class="w-full sm:w-auto text-right shrink-0 relative z-10 flex flex-col items-center sm:items-end">
                <button id="pay-button" class="w-full sm:w-auto px-8 py-3.5 bg-orange-600 hover:bg-orange-700 text-white font-black text-xs uppercase tracking-widest rounded-xl shadow-[0_8px_20px_-6px_rgba(234,88,12,0.6)] hover:shadow-[0_12px_25px_-6px_rgba(234,88,12,0.8)] transition-all animate-pulse transform hover:-translate-y-1 flex items-center justify-center gap-3 ring-4 ring-orange-100">
                    <i class="fa-solid fa-credit-card text-base"></i> BAYAR DP SEKARANG
                </button>
                <p class="text-[9px] text-red-500 font-bold tracking-widest uppercase mt-3 text-center">
                    <i class="fa-solid fa-triangle-exclamation"></i> Menunggu Pembayaran via Midtrans
                </p>
            </div>
        </div>
        @endif

        <!-- ─── JALUR PROGRESS STEPPER TRACKER (FIX AMAN GANDA) ─── -->
        <div class="p-8 sm:p-12 overflow-x-auto bg-white relative z-10 border-t border-slate-100">
            <div class="flex justify-between items-center relative min-w-[600px] max-w-3xl mx-auto px-6">
                <div class="absolute h-1 bg-slate-100 top-5 left-0 right-0 -z-0 rounded-full"></div>
                
                @php
                    $cleanStatus = strtolower($order->status);
                    // Jaring Pengaman Ganda: Jika status terdeteksi 'done' atau 'selesai', set kemajuan penuh ke index 4
                    $isFinished = in_array($cleanStatus, ['done', 'selesai']);
                    
                    $statusSteps = ['pending', 'confirmed', 'cooking', 'shipping', 'done'];
                    $currentIdx = $isFinished ? 4 : array_search($cleanStatus, $statusSteps);
                    if($currentIdx === false && $cleanStatus == 'lunas dp') $currentIdx = 1;
                    if($currentIdx === false && $cleanStatus == 'dimasak') $currentIdx = 2;
                    if($currentIdx === false && $cleanStatus == 'dikirim') $currentIdx = 3;
                    
                    $icons = ['fa-file-invoice-dollar', 'fa-check-to-slot', 'fa-fire-burner', 'fa-truck-fast', 'fa-flag-checkered'];
                @endphp

                @foreach(['Dibuat', 'Konfirmasi', 'Dimasak', 'Dikirim', 'Selesai'] as $key => $label)
                <div class="relative z-10 flex flex-col items-center w-20">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-xs transition-all duration-500
                        @if($key < $currentIdx || $isFinished)
                            bg-green-500 text-white shadow-lg shadow-green-100
                        @elseif($key == $currentIdx && !$isFinished)
                            bg-orange-600 text-white shadow-lg shadow-orange-200 ring-4 ring-orange-100 @if($key !== 0) animate-pulse @endif
                        @else
                            bg-slate-100 text-slate-400
                        @endif
                    ">
                        <i class="fa-solid {{ ($key < $currentIdx || $isFinished) && $key != 4 ? 'fa-check' : $icons[$key] }}"></i>
                    </div>
                    <p class="text-[9px] font-black mt-2.5 uppercase tracking-wider text-center
                        @if($key <= $currentIdx || $isFinished) text-slate-800 @else text-slate-400 @endif">
                        {{ $label }}
                    </p>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ================= 🌟 SEKSI REVISI: BERIKAN PENILAIAN PER MENU ITEM ================= --}}
    @if($isFinished)
    <div class="bg-white rounded-[2rem] p-6 sm:p-8 shadow-sm border border-slate-100 space-y-6">
        <div>
            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                Berikan Penilaian Anda <i class="fa-solid fa-star text-yellow-400"></i>
            </h3>
            <p class="text-xs text-slate-500 mt-1">Ulasan Anda sangat berharga untuk meningkatkan kualitas rasa hidangan dapur Ilin Catering.</p>
        </div>

        <div class="grid grid-cols-1 gap-4">
            @foreach($order->items as $item)
                @php
                    // Cek ketersediaan data ulasan di database
                    $alreadyReviewed = \App\Models\Review::where('user_id', auth()->id())
                                                        ->where('menu_id', $item->menu_id)
                                                        ->where('order_id', $order->id)
                                                        ->first();
                @endphp

                <!-- Box Card Per Menu Item Bertenaga AlpineJS -->
                <div class="bg-slate-50/60 p-5 rounded-2xl border border-slate-100 flex flex-col space-y-4" x-data="{ openForm: false, rating: 5, hoverRating: 0 }">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        
                        <!-- Info Menu Kiri -->
                        <div class="flex items-center gap-3">
                            @if($item->menu && $item->menu->image)
                                <img src="{{ asset('storage/' . $item->menu->image) }}" class="w-12 h-12 rounded-xl object-cover shadow-sm border border-white">
                            @else
                                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center text-orange-600 font-bold text-xs">🍱</div>
                            @endif
                            <div>
                                <h4 class="font-bold text-slate-800 text-sm">"{{ $item->menu->title ?? 'Menu Pilihan' }}"</h4>
                                <p class="text-[11px] text-slate-400 font-medium">Kuantitas: {{ $item->quantity }} Porsi</p>
                            </div>
                        </div>

                        <!-- Tombol Kondisional Kanan -->
                        <div class="w-full sm:w-auto text-right">
                            @if($alreadyReviewed)
                                <span class="inline-flex items-center gap-1 bg-green-50 border border-green-200 px-3 py-1.5 rounded-xl text-green-700 text-xs font-bold">
                                    <i class="fa-solid fa-circle-check text-[10px]"></i> Sudah Diulas (⭐ {{ $alreadyReviewed->rating }})
                                </span>
                            @else
                                <button @click="openForm = !openForm" class="w-full sm:w-auto bg-slate-900 hover:bg-orange-600 text-white text-xs font-black px-4 py-2.5 rounded-xl transition flex items-center justify-center gap-2 outline-none">
                                    <i class="fa-solid fa-pen-to-square text-[10px]"></i> Tulis Ulasan Menu
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Slide Form Pengisian Review Terenkripsi Tunggal -->
                    <div x-show="openForm" x-cloak x-transition class="border-t border-slate-200/60 pt-4 space-y-4">
                        <form action="{{ route('pelanggan.review.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <input type="hidden" name="order_id" value="{{ $order->id }}">
                            <input type="hidden" name="menu_id" value="{{ $item->menu_id }}">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Pilihan Bintang Premium -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Pilih Rating Bintang:</label>
                                    <div class="flex items-center gap-1 text-xl text-slate-200">
                                        <template x-for="i in 5">
                                            <button type="button" @click="rating = i" @mouseenter="hoverRating = i" @mouseleave="hoverRating = 0" class="transition focus:outline-none scale-100 hover:scale-125">
                                                <i class="fa-solid fa-star cursor-pointer" :class="(hoverRating ? i <= hoverRating : i <= rating) ? 'text-yellow-400' : 'text-slate-200'"></i>
                                            </button>
                                        </template>
                                        <input type="hidden" name="rating" :value="rating">
                                    </div>
                                </div>

                                <!-- Upload Foto Kuliner -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Foto Masakan Nyata (Opsional):</label>
                                    <input type="file" name="image" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100 cursor-pointer">
                                </div>
                            </div>

                            <!-- Keterangan Profil Pekerjaan -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Keterangan Profil (Contoh: Mahasiswa / Karyawan PT Arutmin):</label>
                                <input type="text" name="user_title" placeholder="Masukkan pekerjaan atau label label Anda..." class="w-full p-2.5 bg-white border border-slate-200 rounded-xl text-xs font-medium outline-none focus:ring-2 focus:ring-orange-500">
                            </div>

                            <!-- Ulasan Catatan Komentar Textarea -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Bagaimana rasanya? Tuliskan ulasan Anda:</label>
                                <textarea name="comment" rows="2" class="w-full p-3 bg-white border border-slate-200 rounded-xl text-xs outline-none focus:ring-2 focus:ring-orange-500 font-medium text-slate-700" placeholder="Tulis rincian kritik saran rasa hidangan di sini..." required></textarea>
                            </div>

                            <div class="flex justify-end gap-2 text-xs">
                                <button type="button" @click="openForm = false" class="bg-slate-200 text-slate-700 px-4 py-2 rounded-xl font-bold hover:bg-slate-300 transition">Batal</button>
                                <button type="submit" class="bg-orange-600 text-white px-5 py-2 rounded-xl font-bold hover:bg-orange-700 transition shadow-md">Kirim Ulasan</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

</div>

{{-- Script Otomatis Midtrans Gateway Bawaan Aplikasi Kamu --}}
@if($order->payment_status !== 'settlement' && !in_array(strtolower($order->status), ['done', 'selesai']))
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.clientKey') }}"></script>
<script type="text/javascript">
    const payButton = document.getElementById('pay-button');
    if(payButton) {
        payButton.onclick = function() {
            window.snap.pay('{{ $order->snap_token }}', {
                onSuccess: function(result) { window.location.reload(); },
                onPending: function(result) { window.location.reload(); },
                onError: function(result) { window.location.reload(); }
            });
        };
    }
</script>
@endif

@endsection