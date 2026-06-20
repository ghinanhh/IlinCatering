@extends('layouts.app')

@section('content')
@php
    // 🛒 KABEL OTOMATIS: Ambil semua data keranjang pelanggan
    $cartItems = \App\Models\Cart::where('user_id', auth()->id())->get()->keyBy('menu_id');

    // 🌟 FIX URUTAN KATEGORI: Dipaksa berurutan dari Nasi Box (box) -> Prasmanan (prasmanan) -> Snack Box (snack)
    $categoryOrder = ['box', 'prasmanan', 'snack'];
    $groupedMenus = $menus->groupBy('category')->sortBy(function ($value, $key) use ($categoryOrder) {
        return array_search($key, $categoryOrder);
    });

    $categoryLabels = [
        'box' => 'Paket Nasi Box 🍱',
        'prasmanan' => 'Paket Prasmanan 🍲',
        'snack' => 'Paket Snack Box 🥐'
    ];
@endphp

<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
    <div>
        <h1 class="text-3xl font-extrabold text-slate-900">Daftar Menu Lezat 🍱</h1>
        <p class="text-slate-500">Pilih menu favoritmu dan nikmati hidangan kualitas restoran.</p>
    </div>
    
    <form action="{{ route('pelanggan.menu') }}" method="GET" class="relative w-full md:w-80">
        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
            <i class="fa-solid fa-magnifying-glass"></i>
        </span>

        @if(request('category'))
            <input type="hidden" name="category" value="{{ request('category') }}">
        @endif

        <input 
            type="text" 
            name="search" 
            value="{{ request('search') }}" 
            placeholder="Cari menu atau kategori..." 
            class="w-full pl-10 pr-4 py-3 bg-white border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-orange-500 transition shadow-sm"
        >
    </form>
</div>

<div class="flex flex-wrap gap-2 mb-8 border-b border-slate-200 pb-6">
    <a href="{{ route('pelanggan.menu') }}" class="px-5 py-2.5 rounded-full text-xs font-bold transition {{ !request('category') ? 'bg-slate-900 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
        Semua Menu
    </a>
    <a href="{{ route('pelanggan.menu', ['category' => 'box', 'search' => request('search')]) }}" class="px-5 py-2.5 rounded-full text-xs font-bold transition {{ request('category') == 'box' ? 'bg-orange-600 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
        Nasi Box
    </a>
    <a href="{{ route('pelanggan.menu', ['category' => 'prasmanan', 'search' => request('search')]) }}" class="px-5 py-2.5 rounded-full text-xs font-bold transition {{ request('category') == 'prasmanan' ? 'bg-orange-600 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
        Prasmanan
    </a>
    <a href="{{ route('pelanggan.menu', ['category' => 'snack', 'search' => request('search')]) }}" class="px-5 py-2.5 rounded-full text-xs font-bold transition {{ request('category') == 'snack' ? 'bg-orange-600 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
        Snack Box
    </a>
</div>

{{-- Container untuk memuat pop-up jika dipicu oleh aksi lokal porsi --}}
<div id="local-toast-container"></div>

{{-- Pop-up bawaan jika dipicu oleh session Laravel --}}
@if(session('success'))
<div id="toast-success" class="fixed bottom-6 right-6 z-50 flex items-center w-full max-w-xs p-4 bg-white border border-slate-100 border-l-4 border-l-green-600 rounded-r-2xl rounded-l-md shadow-[0_10px_30px_rgba(0,0,0,0.15)] transform translate-y-0 opacity-100 transition-all duration-500" role="alert">
    <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-white bg-green-600 rounded-xl shadow-sm">
        <i class="fa-solid fa-check text-sm"></i>
    </div>
    <div class="ms-3 text-[11px] font-black text-slate-800 uppercase tracking-wider">
        {{ session('success') }}
    </div>
    <button type="button" onclick="document.getElementById('toast-success').remove()" class="ms-auto text-slate-400 hover:text-slate-900 rounded-lg p-1 hover:bg-slate-50 inline-flex items-center justify-center h-7 w-7 outline-none">
        <i class="fa-solid fa-xmark text-xs"></i>
    </button>
</div>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Cek notifikasi mandiri dari localStorage (Penyelamat pas klik + / - / Enter manual)
        const localMsg = localStorage.getItem('toast_success_msg');
        if (localMsg) {
            const container = document.getElementById('local-toast-container');
            const isWarning = localMsg.includes('Minimal');
            const borderColor = isWarning ? 'border-l-amber-500' : 'border-l-green-600';
            const bgColor = isWarning ? 'bg-amber-600' : 'bg-green-600';
            const icon = isWarning ? 'fa-exclamation' : 'fa-check';

            container.innerHTML = `
                <div id="toast-local" class="fixed bottom-6 right-6 z-50 flex items-center w-full max-w-xs p-4 bg-white border border-slate-100 border-l-4 ${borderColor} rounded-r-2xl rounded-l-md shadow-[0_10px_30px_rgba(0,0,0,0.15)] transform translate-y-0 opacity-100 transition-all duration-500" role="alert">
                    <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-white ${bgColor} rounded-xl shadow-sm">
                        <i class="fa-solid ${icon} text-sm"></i>
                    </div>
                    <div class="ms-3 text-[11px] font-black text-slate-800 uppercase tracking-wider">
                        ${localMsg}
                    </div>
                    <button type="button" onclick="document.getElementById('toast-local').remove()" class="ms-auto text-slate-400 hover:text-slate-900 rounded-lg p-1 hover:bg-slate-50 inline-flex items-center justify-center h-7 w-7 outline-none">
                        <i class="fa-solid fa-xmark text-xs"></i>
                    </button>
                </div>
            `;
            setTimeout(() => {
                const toast = document.getElementById('toast-local');
                if(toast) {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateY(20px)';
                    setTimeout(() => toast.remove(), 500);
                }
            }, 3000);
            localStorage.removeItem('toast_success_msg');
        }

        // 2. Hide otomatis toast bawaan session Laravel
        const sessionToast = document.getElementById('toast-success');
        if (sessionToast) {
            setTimeout(() => {
                sessionToast.style.opacity = '0';
                sessionToast.style.transform = 'translateY(20px)';
                setTimeout(() => sessionToast.remove(), 500);
            }, 3000);
        }
    });
</script>

@forelse($groupedMenus as $category => $items)
    <div class="mb-12">
        <h2 class="text-xl font-black text-slate-800 mb-6 flex items-center gap-3">
            {{ $categoryLabels[$category] ?? ucfirst($category) }}
            <span class="text-xs bg-orange-100 text-orange-600 px-3 py-1 rounded-full">{{ $items->count() }} Pilihan</span>
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($items as $menu)
                <div class="bg-white p-4 rounded-[2.5rem] shadow-sm border border-slate-100 group hover:shadow-xl transition-all duration-300">
                    <div class="relative overflow-hidden rounded-[2rem] h-48 mb-4">
                        <img 
                            src="{{ asset('storage/' . $menu->image) }}" 
                            class="w-full h-full object-cover group-hover:scale-110 transition duration-500"
                        >
                        <div class="absolute top-3 right-3 bg-white/90 backdrop-blur px-3 py-1 rounded-full text-[10px] font-bold text-orange-600 shadow-sm uppercase">
                            {{ $menu->category }}
                        </div>
                    </div>

                    <div class="px-2">
                        <h3 class="font-bold text-lg text-slate-900 mt-1 truncate">
                            {{ $menu->title }}
                        </h3>

                        <p class="text-slate-500 text-xs mt-2 line-clamp-2">
                            {{ $menu->description }}
                        </p>
                        
                        @if(isset($cartItems[$menu->id]))
                            @php $currentCart = $cartItems[$menu->id]; @endphp
                            
                            <form action="{{ route('pelanggan.cart.updateQuantity', $currentCart->id) }}" method="POST" class="mt-5 w-full">
                                @csrf
                                <div class="flex justify-between items-center mb-3 gap-2 h-10">
                                    <p class="text-lg font-extrabold text-orange-600">
                                        Rp {{ number_format($menu->price, 0, ',', '.') }}
                                    </p>
                                    
                                    <div class="flex items-center bg-slate-100 p-0.5 rounded-xl gap-1 border border-slate-200 shadow-inner shrink-0">
                                        {{-- Atribut onclick diatur agar merekam teks pop-up sukses atau peringatan jika bernilai 1 --}}
                                        <button type="submit" name="action" value="decrease" onclick="let qty = this.parentNode.querySelector('input').value; if(qty > 1) { localStorage.setItem('toast_success_msg', 'Jumlah pesanan berhasil diperbarui!'); } else { localStorage.setItem('toast_success_msg', 'Minimal pemesanan adalah 1 porsi!'); }" class="w-7 h-7 bg-white text-slate-800 rounded-lg hover:bg-red-500 hover:text-white transition flex items-center justify-center font-black shadow-sm outline-none shrink-0">
                                            <i class="fa-solid fa-minus text-[9px]"></i>
                                        </button>

                                        {{-- Diperbaiki penanganan onchange dan onkeydown Enter agar pemicu pop-up bekerja harmonis --}}
                                        <input 
                                            type="number" 
                                            name="quantity" 
                                            value="{{ $currentCart->quantity }}" 
                                            min="1" 
                                            onchange="localStorage.setItem('toast_success_msg', 'Jumlah pesanan berhasil diperbarui!'); this.form.submit()" 
                                            onkeydown="if(event.key === 'Enter') { localStorage.setItem('toast_success_msg', 'Jumlah pesanan berhasil diperbarui!'); event.preventDefault(); this.blur(); }"
                                            class="w-8 text-center font-bold text-slate-900 text-xs bg-transparent border-none focus:ring-0 outline-none p-0 no-spinners"
                                        >

                                        <button type="submit" name="action" value="increase" onclick="localStorage.setItem('toast_success_msg', 'Jumlah pesanan berhasil diperbarui!')" class="w-7 h-7 bg-white text-slate-800 rounded-lg hover:bg-green-600 hover:text-white transition flex items-center justify-center font-black shadow-sm outline-none shrink-0">
                                            <i class="fa-solid fa-plus text-[9px]"></i>
                                        </button>
                                    </div>
                                </div>

                                <button 
                                    type="submit"
                                    onclick="localStorage.setItem('toast_success_msg', 'Jumlah pesanan berhasil diperbarui!')"
                                    class="w-full py-2 bg-slate-900 text-white rounded-xl hover:bg-orange-600 transition shadow-sm flex items-center justify-center gap-2 text-[11px] font-bold uppercase tracking-wide outline-none"
                                >
                                    <i class="fa-solid fa-cart-plus text-xs"></i>
                                    <span>Tambah ke Keranjang</span>
                                </button>
                            </form>
                        @else
                            <form action="{{ route('cart.add', $menu->id) }}" method="POST" class="mt-5 w-full">
                                @csrf
                                <div class="flex justify-between items-center mb-3 h-10">
                                    <p class="text-lg font-extrabold text-orange-600">
                                        Rp {{ number_format($menu->price, 0, ',', '.') }}
                                    </p>
                                </div>

                                <button 
                                    type="submit"
                                    class="w-full py-2 bg-slate-900 text-white rounded-xl hover:bg-orange-600 transition shadow-sm flex items-center justify-center gap-2 text-[11px] font-bold uppercase tracking-wide outline-none"
                                >
                                    <i class="fa-solid fa-cart-plus text-xs"></i>
                                    <span>Tambah ke Keranjang</span>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@empty
    <div class="col-span-full text-center py-20 text-slate-400">
        <i class="fa-solid fa-box-open text-4xl mb-3 block text-slate-300"></i>
        <p class="italic font-medium">Belum ada menu yang sesuai dengan pencarian ini.</p>
    </div>
@endforelse

<style>
    .no-spinners::-webkit-outer-spin-button,
    .no-spinners::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    .no-spinners {
        -moz-appearance: textfield;
    }
</style>
@endsection