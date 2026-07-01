@extends('layouts.app')

@section('content')
{{-- 🌟 CDN KALENDER FLATPICKR --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<div class="mb-8">
    <h1 class="text-3xl font-extrabold text-slate-900">Keranjang Belanja 🛒</h1>
    <p class="text-slate-500">Tinjau pesananmu sebelum lanjut ke pembayaran.</p>
</div>

{{-- 🌟 FORM UTAMA CHECKOUT DENGAN ALPINE JS --}}
<form action="{{ route('pelanggan.checkout.process') }}" method="POST" x-data="{ paymentMethod: 'dp' }">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-2 space-y-4">
            @php $totalTagihan = 0; $totalPorsi = 0; @endphp

            @forelse($cartItems as $item)
                @php
                    $subtotal = $item->menu->price * $item->quantity;
                    $totalTagihan += $subtotal;
                    $totalPorsi += $item->quantity;
                @endphp

                <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 space-y-4">
                    <div class="flex flex-col md:flex-row items-center gap-6">
                        <img src="{{ asset('storage/' . $item->menu->image) }}" class="w-24 h-24 rounded-2xl object-cover">

                        <div class="flex-1 text-center md:text-left">
                            <h3 class="font-bold text-lg text-slate-900">
                                {{ $item->menu->title }}
                            </h3>
                            <p class="text-orange-600 font-bold">
                                Rp {{ number_format($item->menu->price, 0, ',', '.') }}
                            </p>
                        </div>

                        {{-- Tombol Kuantitas --}}
                        <div class="flex items-center gap-4 bg-slate-50 p-2 rounded-2xl border border-slate-200">
                            @if($item->quantity > 1)
                                <button form="decrease-form-{{ $item->id }}" type="submit" class="w-8 h-8 flex items-center justify-center bg-white rounded-xl shadow-sm hover:text-orange-600 transition text-xs font-bold outline-none cursor-pointer">
                                    -
                                </button>
                            @else
                                <button type="button" disabled class="w-8 h-8 flex items-center justify-center bg-slate-100 rounded-xl text-slate-300 cursor-not-allowed text-xs font-bold outline-none" title="Minimal pemesanan 1 porsi">
                                    -
                                </button>
                            @endif

                            <input type="number" value="{{ $item->quantity }}" min="1"
                                onkeydown="if(event.key === 'Enter') { event.preventDefault(); this.blur(); }"
                                onchange="const f = document.getElementById('manual-form-{{ $item->id }}'); if(f) { f.querySelector('.manual-qty').value = this.value; f.submit(); }"
                                class="w-12 text-center bg-white border border-slate-200 rounded-lg font-bold text-slate-800 focus:ring-2 focus:ring-orange-500 focus:outline-none p-1 text-xs [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                                title="Masukkan jumlah porsi lalu klik di luar kotak atau tekan enter">

                            <button form="increase-form-{{ $item->id }}" type="submit" class="w-8 h-8 flex items-center justify-center bg-white rounded-xl shadow-sm hover:text-orange-600 transition text-xs font-bold outline-none cursor-pointer">
                                +
                            </button>
                        </div>

                        <div class="text-right">
                            <p class="text-sm text-slate-400">Subtotal</p>
                            <p class="font-black text-slate-900">
                                Rp {{ number_format($subtotal, 0, ',', '.') }}
                            </p>
                        </div>

                        <button form="delete-form-{{ $item->id }}" type="submit" class="text-slate-300 hover:text-red-500 transition text-xl bg-transparent border-none p-0 cursor-pointer outline-none" title="Hapus dari keranjang">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </div>

                    <div class="pt-4 border-t border-slate-100">
                        <label class="block text-[10px] font-black uppercase text-slate-400 mb-1.5 tracking-wider">
                            <i class="fa-regular fa-comment-dots text-orange-500 mr-1"></i> Catatan Khusus Menu Ini:
                        </label>
                        <textarea name="cart_notes[{{ $item->id }}]" rows="2" 
    class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium outline-none focus:ring-2 focus:ring-orange-500 placeholder-slate-400 text-slate-700" 
    placeholder="Contoh: Request pedas manis...">{{ $item->notes }}</textarea>
                    </div>
                </div>
            @empty
                <div class="bg-white p-12 md:p-16 rounded-[2.5rem] shadow-sm border border-slate-100 text-center flex flex-col items-center justify-center min-h-[400px]">
                    <div class="w-20 h-20 bg-orange-50 text-orange-600 rounded-3xl flex items-center justify-center mb-6 shadow-sm">
                        <i class="fa-solid fa-basket-shopping text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-black text-slate-800 mb-2">Keranjang Belanja Kosong</h3>
                    <p class="text-slate-400 text-xs max-w-sm mb-8 leading-relaxed">
                        Belum ada menu lezat yang kamu pilih nih. Yuk, intip daftar menu katering kami dan mulai pilih masakan favoritmu!
                    </p>
                    <a href="{{ route('pelanggan.menu') }}" class="px-6 py-3 bg-slate-900 text-white rounded-xl text-xs font-bold uppercase tracking-wider hover:bg-orange-600 transition shadow-sm outline-none">
                        Mulai Pilih Menu 🍱
                    </a>
                </div>
            @endforelse

            @if($cartItems->isNotEmpty())
                <a href="{{ route('pelanggan.menu') }}" class="flex items-center justify-center gap-2 p-4 border-2 border-dashed border-slate-200 rounded-[2rem] text-slate-400 hover:text-orange-600 hover:border-orange-600 transition group font-bold">
                    <i class="fa-solid fa-plus group-hover:rotate-90 transition duration-300"></i>
                    Tambah Menu Lainnya
                </a>
            @endif
        </div>

        <div class="lg:col-span-1">
            <div class="bg-white p-8 rounded-[2.5rem] shadow-lg border border-slate-100 sticky top-24 space-y-6">
                <h2 class="text-xl font-extrabold text-slate-900">Ringkasan Pesanan</h2>

                <div class="space-y-4">
                    <div class="flex justify-between text-slate-500">
                        <span>Total Harga</span>
                        <span class="font-bold text-slate-900">
                            Rp {{ number_format($totalTagihan, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="flex justify-between text-slate-500">
                        <span>Biaya Pengiriman</span>
                        <span class="font-bold text-green-600">Gratis</span>
                    </div>

                    <div class="border-t border-slate-100 pt-4 flex justify-between items-center">
                        <span class="text-lg font-bold text-slate-900">Total Tagihan</span>
                        <span class="text-2xl font-black text-orange-600">
                            Rp {{ number_format($totalTagihan, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                {{-- Opsi Opsi Pembayaran --}}
                <div class="bg-orange-50 p-5 rounded-2xl border border-orange-100 space-y-3">
                    <div class="flex items-center gap-2 text-orange-700 font-bold text-sm">
                        <i class="fa-solid fa-wallet"></i>
                        <span>Pilih Opsi Pembayaran</span>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-2 mt-2">
                        <label class="flex items-center justify-center gap-2 p-3 bg-white border rounded-xl cursor-pointer transition select-none" :class="paymentMethod === 'dp' ? 'border-orange-500 ring-2 ring-orange-200' : 'border-slate-200'">
                            <input type="radio" name="payment_option" value="dp" x-model="paymentMethod" class="text-orange-600 focus:ring-orange-500" checked>
                            <span class="text-xs font-bold text-slate-700">Bayar DP 30%</span>
                        </label>
                        
                        <label class="flex items-center justify-center gap-2 p-3 bg-white border rounded-xl cursor-pointer transition select-none" :class="paymentMethod === 'lunas' ? 'border-orange-500 ring-2 ring-orange-200' : 'border-slate-200'">
                            <input type="radio" name="payment_option" value="lunas" x-model="paymentMethod" class="text-orange-600 focus:ring-orange-500">
                            <span class="text-xs font-bold text-slate-700">Bayar Lunas</span>
                        </label>
                    </div>

                    <div class="mt-3 flex justify-between items-center bg-white p-3 rounded-xl shadow-sm border border-orange-100">
                        <span class="text-[10px] font-bold text-slate-500 uppercase">Wajib Bayar Sekarang:</span>
                        <span class="font-black text-slate-900 text-sm" x-show="paymentMethod === 'dp'">
                            Rp {{ number_format($totalTagihan * 0.3, 0, ',', '.') }}
                        </span>
                        <span class="font-black text-slate-900 text-sm" x-show="paymentMethod === 'lunas'">
                            Rp {{ number_format($totalTagihan, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                @if($totalTagihan > 0)
                    <div class="space-y-4 border-t border-slate-100 pt-4">
                        <h3 class="font-bold text-slate-900 italic underline decoration-orange-500 decoration-4">Informasi Pengiriman</h3>
                        
                        <div>
                            <label class="text-[10px] font-bold text-slate-400 uppercase">Nama Penerima</label>
                            <input type="text" name="recipient_name" value="{{ Auth::user()->name }}" required placeholder="Nama lengkap penerima" 
                                class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-orange-500 outline-none">
                        </div>

                        <div>
                            <label class="text-[10px] font-bold text-slate-400 uppercase">Nomor HP (WhatsApp)</label>
                            <input type="number" name="phone_number" required placeholder="Contoh: 0887435414960" 
                                class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-orange-500 outline-none">
                        </div>

                        <div>
                            <label class="text-[10px] font-bold text-slate-400 uppercase">Alamat Lengkap</label>
                            <textarea name="address" required placeholder="Jl. Nama Jalan, No. Rumah, RT/RW, Kec..." 
                                class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-orange-500 outline-none h-20"></textarea>
                        </div>
                    </div>

                    {{-- Kalender Inline Flatpickr --}}
                    <div class="space-y-3 pt-4 border-t border-slate-100">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block">Pilih Tanggal Acara</label>
                        
                        <div class="w-full flex justify-center bg-slate-50 p-2 rounded-2xl border border-slate-200">
                            <input type="text" name="event_date" id="event_calendar" required class="hidden">
                        </div>
                        
                        <div class="flex flex-wrap items-center gap-3 text-[9px] font-black uppercase tracking-wider px-1">
                            <div class="flex items-center gap-1 text-slate-500">
                                <span class="w-2.5 h-2.5 bg-white border border-slate-300 rounded-full inline-block"></span> Tersedia
                            </div>
                            <div class="flex items-center gap-1 text-red-600">
                                <span class="w-2.5 h-2.5 bg-red-100 border border-red-300 rounded-full inline-block"></span> Penuh / Terisi
                            </div>
                            <div class="flex items-center gap-1 text-orange-600">
                                <span class="w-2.5 h-2.5 bg-orange-600 rounded-full inline-block"></span> Pilihanmu
                            </div>
                        </div>

                        <div class="pt-2">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-1">Jam Pengiriman</label>
                            <input type="time" name="event_time" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-orange-500 outline-none cursor-pointer">
                        </div>

                        <p class="text-[9px] text-red-500 font-bold italic leading-tight mt-2">
                            *Pemesanan wajib dilakukan minimal H-3 dari tanggal acara untuk keperluan persiapan bahan segar dapur katering kami.
                        </p>
                    </div>

                    {{-- Tombol Submit Form --}}
                    @if($totalPorsi < 10)
                        <div class="bg-red-50 p-4 rounded-2xl border border-red-100 text-red-700 text-xs font-medium space-y-1 mt-4">
                            <div class="flex items-center gap-2 font-bold text-red-800 text-sm">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                                <span>Minimal Order Belum Tercapai</span>
                            </div>
                            <p>Total pesanan Anda baru <strong>{{ $totalPorsi }} porsi</strong>. Ilin Catering menerapkan batas minimal porsi sebanyak <strong>10 porsi</strong>.</p>
                        </div>

                        <button type="button" class="w-full py-4 bg-slate-300 text-white rounded-2xl font-bold cursor-not-allowed flex items-center justify-center gap-3 mt-4 shadow-none" disabled>
                            Konfirmasi & Checkout (Min. 10 Porsi)
                            <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    @else
                        <button type="submit" class="w-full py-4 bg-orange-600 text-white rounded-2xl font-bold shadow-lg hover:bg-orange-700 transition flex items-center justify-center gap-3 mt-6 outline-none">
                            Konfirmasi & Checkout
                            <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    @endif
                @else
                    <button class="w-full py-4 bg-slate-300 text-white rounded-2xl font-bold cursor-not-allowed flex items-center justify-center gap-3 shadow-none" disabled>
                        Checkout Sekarang
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                @endif
            </div>
        </div>
    </div>
</form>

{{-- FORM RAHASIA UPDATE & HAPUS QUANTITY --}}
@foreach($cartItems as $item)
    <form id="delete-form-{{ $item->id }}" action="{{ route('pelanggan.cart.removeItem', $item->id) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
@endforeach

@foreach($cartItems as $item)
    <form id="decrease-form-{{ $item->id }}" action="{{ route('pelanggan.cart.updateQuantity', $item->id) }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="action" value="decrease">
    </form>

    <form id="increase-form-{{ $item->id }}" action="{{ route('pelanggan.cart.updateQuantity', $item->id) }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="action" value="increase">
    </form>
@endforeach

@foreach($cartItems as $item)
    <form id="manual-form-{{ $item->id }}" action="{{ route('pelanggan.cart.updateQuantity', $item->id) }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="id" value="{{ $item->id }}">
        <input type="hidden" name="action" value="manual">
        <input type="hidden" name="qty" class="manual-qty" value="{{ $item->quantity }}">
    </form>
@endforeach

{{-- SCRIPT KALENDER INTEGRASI --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const bookedDates = @json($bookedDates ?? []);
        
        flatpickr("#event_calendar", {
            inline: true,
            minDate: new Date().fp_incr(3),
            dateFormat: "Y-m-d",
            disable: bookedDates,
            locale: { firstDayOfWeek: 1 }
        });
    });
</script>

<style>
    .flatpickr-calendar {
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        width: 100% !important;
        max-width: 300px !important;
    }
    .flatpickr-months .flatpickr-month, .flatpickr-current-month .flatpickr-monthDropdown-months {
        font-weight: 800 !important;
        color: #0f172a !important;
    }
    .flatpickr-day.flatpickr-disabled, .flatpickr-day.flatpickr-disabled:hover {
        background: #fee2e2 !important;
        color: #ef4444 !important;
        border-color: #fee2e2 !important;
        text-decoration: line-through;
        cursor: not-allowed !important;
        font-weight: bold;
    }
    .flatpickr-day.selected, .flatpickr-day.selected:hover {
        background: #ea580c !important;
        border-color: #ea580c !important;
        color: white !important;
    }
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
</style>
@endsection