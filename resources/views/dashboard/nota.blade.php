@extends('layouts.app')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 no-print">
    <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Nota Pesanan 🧾</h1>
        <p class="text-sm text-slate-500 mt-1">Bukti rincian tagihan pemesanan Ilin Catering.</p>
    </div>
    <div class="flex gap-3">
        {{-- 🌟 FIX: Tombol kembali pintar, menyesuaikan role (Admin ke Arsip, Pelanggan ke Dashboard) --}}
        <a href="{{ auth()->user()->role == 'admin' ? route('admin.orders.archive') : route('pelanggan.dashboard') }}" class="bg-white text-slate-700 border border-slate-200 px-5 py-2.5 rounded-xl font-bold shadow-sm hover:bg-slate-50 transition flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
        <button onclick="window.print()" class="bg-orange-600 hover:bg-orange-700 text-white px-5 py-2.5 rounded-xl font-bold shadow-md transition flex items-center gap-2">
            <i class="fa-solid fa-print"></i> Cetak / Simpan PDF
        </button>
    </div>
</div>

<div class="bg-white p-8 sm:p-12 rounded-[2rem] shadow-sm border border-slate-100 w-full max-w-4xl mx-auto print-area relative">
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-b-2 border-slate-100 pb-6 mb-8 gap-6">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full border-2 border-slate-100 p-1 flex items-center justify-center bg-white shrink-0">
                <img src="{{ asset('images/logo-ilin.png') }}" alt="Logo" class="max-w-full max-h-full object-contain">
            </div>
            <div>
                <h2 class="text-2xl sm:text-3xl font-black text-orange-600 uppercase tracking-tighter">ILIN CATERING</h2>
                <p class="text-xs sm:text-sm font-medium text-slate-500">Layanan Katering Terpercaya</p>
            </div>
        </div>
        <div class="text-left sm:text-right">
            {{-- 🌟 LOGIKA OTOMATIS: Judul Nota berubah --}}
            @if(in_array($order->status, ['done', 'selesai', 'Selesai']))
                <h2 class="text-xl sm:text-2xl font-black text-slate-900 uppercase tracking-widest">NOTA LUNAS</h2>
            @else
                <h2 class="text-xl sm:text-2xl font-black text-slate-900 uppercase tracking-widest">NOTA PESANAN</h2>
            @endif
            <p class="text-sm font-bold text-slate-400 mt-1">#{{ $order->order_number }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 mb-8">
        <div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Ditagihkan Kepada:</p>
            <h3 class="text-lg font-extrabold text-slate-900">{{ $order->recipient_name ?? $order->user->name }}</h3>
            <p class="text-sm text-slate-600 font-medium mt-1 flex items-center gap-2">
                <i class="fa-brands fa-whatsapp text-green-500"></i> {{ $order->phone_number }}
            </p>
            <p class="text-xs text-slate-500 mt-2 leading-relaxed max-w-xs">
                {{ $order->address }}
            </p>
        </div>
        <div class="sm:text-right">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Jadwal Pengiriman:</p>
            <h3 class="text-sm font-extrabold text-slate-900">
                {{ \Carbon\Carbon::parse($order->event_date)->locale('id')->translatedFormat('d F Y') }}
            </h3>
            <p class="text-xs text-slate-600 font-medium mt-1">
                Pukul: {{ \Carbon\Carbon::parse($order->event_time)->format('H:i') }} WITA
            </p>
            
            <div class="mt-4 inline-block text-left sm:text-right">
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1">Status Pembayaran</p>
                {{-- 🌟 LOGIKA OTOMATIS: Badge Status Pembayaran --}}
                @if(in_array($order->status, ['done', 'selesai', 'Selesai']))
                    <span class="bg-green-100 text-green-700 px-3 py-1.5 rounded-lg text-xs font-black uppercase tracking-wider block">LUNAS 100%</span>
                @else
                    <span class="bg-orange-50 text-orange-600 px-3 py-1.5 rounded-lg text-xs font-black uppercase tracking-wider block border border-orange-200">TELAH BAYAR DP</span>
                @endif
            </div>
        </div>
    </div>

    <div class="overflow-x-auto mb-8">
        <table class="w-full text-left">
            <thead>
                <tr class="border-y-2 border-slate-100 text-slate-500 uppercase text-[10px] font-black tracking-widest">
                    <th class="py-4 pl-2">Deskripsi Pesanan</th>
                    <th class="py-4 text-center">Porsi</th>
                    <th class="py-4 text-right">Harga/Porsi</th>
                    <th class="py-4 text-right pr-2">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($order->items as $item)
                <tr>
                    <td class="py-4 pl-2">
                        <p class="font-extrabold text-sm text-slate-900">{{ $item->menu->title }}</p>
                        @if($item->notes)
                            <p class="text-[10px] text-slate-400 italic mt-1 font-medium">Catatan: {{ $item->notes }}</p>
                        @endif
                    </td>
                    <td class="py-4 text-center font-extrabold text-sm text-slate-900">{{ $item->quantity }}x</td>
                    <td class="py-4 text-right text-xs text-slate-500 font-medium">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="py-4 text-right pr-2 font-black text-sm text-slate-900">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="flex justify-end mb-16">
        <div class="w-full sm:w-[350px] bg-slate-50 p-6 rounded-[1.5rem] border border-slate-100">
            {{-- 🌟 LOGIKA OTOMATIS: Hitungan Matematika --}}
            @if(in_array($order->status, ['done', 'selesai', 'Selesai']))
                <div class="flex justify-between items-center mb-3 text-sm">
                    <span class="text-slate-500 font-bold">Total Harga Pesanan</span>
                    <span class="font-black text-slate-900">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center mb-3 text-sm">
                    <span class="text-slate-500 font-bold">Telah Dibayar (100%)</span>
                    <span class="font-black text-green-600">- Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                </div>
                <div class="border-t-2 border-slate-200 pt-4 mt-2 flex justify-between items-center">
                    <span class="text-xs font-black text-slate-900 uppercase tracking-widest">Sisa Tagihan</span>
                    <span class="text-xl font-black text-green-600">Rp 0</span>
                </div>
            @else
                <div class="flex justify-between items-center mb-3 text-sm">
                    <span class="text-slate-500 font-bold">Total Harga Pesanan</span>
                    <span class="font-black text-slate-900">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center mb-3 text-sm">
                    <span class="text-slate-500 font-bold">Telah Dibayar (DP 30%)</span>
                    <span class="font-black text-green-600">- Rp {{ number_format($order->dp_amount, 0, ',', '.') }}</span>
                </div>
                <div class="border-t-2 border-slate-200 pt-4 mt-2 flex justify-between items-center">
                    <span class="text-xs font-black text-slate-900 uppercase tracking-widest">Sisa Tagihan</span>
                    <span class="text-xl font-black text-orange-600">Rp {{ number_format($order->remaining_payment, 0, ',', '.') }}</span>
                </div>
            @endif
        </div>
    </div>

    @if(in_array($order->status, ['done', 'selesai', 'Selesai']))
    <div class="hidden print:block text-right mt-12">
        <p class="text-xs text-slate-500 font-medium">Bati-Bati, {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}</p>
        <p class="text-xs font-bold text-slate-900 mt-1 mb-20">Mengetahui,</p>
        
        <p class="font-black text-slate-900 uppercase inline-block border-b-2 border-slate-900 px-6">Masliani Fitri</p>
        <p class="text-[10px] text-slate-500 font-bold uppercase mt-1 tracking-widest">Pimpinan Ilin Catering</p>
    </div>
    @endif

</div>

<style>
    @media print {
        /* Sembunyikan sidebar, header, dan elemen kontrol */
        nav, aside, .no-print, button, a.no-print { display: none !important; }
        
        /* Reset margin utama layar agar fokus ke kertas nota */
        body { background-color: white !important; margin: 0 !important; padding: 0 !important; }
        main { margin-left: 0 !important; padding: 0 !important; width: 100% !important; }
        
        /* Hilangkan bayangan kotak agar menyatu dengan kertas printer */
        .print-area { box-shadow: none !important; border: none !important; max-width: 100% !important; padding: 0 !important; margin: 0 auto !important; }
        
        /* Paksa Google Chrome mencetak warna latar belakang (Kotak abu-abu dan label status) */
        .bg-slate-50 { background-color: #f8fafc !important; -webkit-print-color-adjust: exact; color-adjust: exact; }
        .bg-orange-50 { background-color: #fff7ed !important; -webkit-print-color-adjust: exact; color-adjust: exact; }
        .bg-green-100 { background-color: #dcfce7 !important; -webkit-print-color-adjust: exact; color-adjust: exact; }
        
        /* Pastikan warna teks penting tidak memudar saat diprint */
        .text-orange-600 { color: #ea580c !important; -webkit-print-color-adjust: exact; color-adjust: exact; }
        .text-green-600 { color: #16a34a !important; -webkit-print-color-adjust: exact; color-adjust: exact; }
        .text-slate-900 { color: #0f172a !important; }
    }
</style>
@endsection