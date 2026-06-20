@extends('layouts.app')

@section('content')
<div class="p-6 bg-slate-50 min-h-screen">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-slate-900">Riwayat Pesanan Selesai ⭐</h1>
        <p class="text-slate-500 mt-2">Daftar arsip seluruh pesanan katering Anda yang telah selesai dan lunas.</p>
    </div>

    {{-- Statistik Cards (Tetap Dipertahankan Agar Selaras dengan Beranda) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
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

    <div class="space-y-6">
        @forelse($orders as $orderItem)
            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden transition-all hover:border-emerald-200">
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
                        {{-- Tombol Review Bawaan --}}
                        <a href="{{ route('pelanggan.checkout', $orderItem->id) }}" class="px-4 py-2 bg-yellow-400 hover:bg-yellow-500 text-slate-900 rounded-xl text-[10px] font-black uppercase transition-all shadow-lg flex items-center gap-2 font-bold">
                            Beri Review ⭐
                        </a>
                        
                        {{-- 📄 Cetak Nota di halaman yang sama tanpa target="_blank" --}}
                        <a href="{{ url('dashboard/checkout/nota/' . $orderItem->id) }}" class="px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-xl text-[10px] font-black uppercase transition-all flex items-center gap-1.5 border border-white/10">
                            <i class="fa-solid fa-file-invoice"></i> Nota Lunas
                        </a>
                    </div>
                </div>
                
                <div class="p-6 bg-slate-50/50 flex justify-between items-center flex-wrap gap-2 text-sm text-slate-600">
                    <p class="font-medium">
                        <i class="fa-solid fa-circle-check text-green-500 mr-1"></i> Pesanan selesai pada {{ $orderItem->updated_at->format('d M Y') }}
                    </p>
                    <p class="font-bold text-slate-900">Total Bayar: Rp {{ number_format($orderItem->total_price, 0, ',', '.') }}</p>
                </div>
            </div>
        @empty
            <div class="bg-white border-2 border-dashed border-slate-200 rounded-[2rem] p-16 text-center text-slate-500 font-medium italic text-sm">
                📁 Belum ada riwayat transaksi pesanan yang selesai.
            </div>
        @endforelse
    </div>
</div>
@endsection