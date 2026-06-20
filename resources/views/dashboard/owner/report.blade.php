@extends('layouts.app')

@section('content')
<div class="mb-8 flex justify-between items-center no-print">
    <div>
        <h1 class="text-3xl font-extrabold text-slate-900">Laporan Penjualan 📈</h1>
        <p class="text-slate-500">Monitoring data pesanan, pembayaran DP, dan pelunasan secara real-time.</p>
    </div>
    <div class="flex gap-3">
        <button onclick="window.print()" class="px-6 py-3 bg-orange-600 text-white rounded-2xl font-bold flex items-center gap-2 hover:bg-orange-700 transition shadow-lg">
            <i class="fa-solid fa-file-pdf"></i>
            Cetak / Simpan PDF
        </button>
    </div>
</div>

<div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm mb-8 no-print">
    <form action="{{ route('owner.report') }}" method="GET" class="flex flex-wrap md:flex-nowrap gap-4 items-end">
        <div class="flex-1">
            <label class="text-[10px] font-bold text-slate-400 uppercase mb-2 block tracking-widest">Periode Awal</label>
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-orange-500">
        </div>
        <div class="flex-1">
            <label class="text-[10px] font-bold text-slate-400 uppercase mb-2 block tracking-widest">Periode Akhir</label>
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-orange-500">
        </div>
        <button type="submit" class="px-6 py-3 bg-slate-900 text-white rounded-xl font-bold hover:bg-slate-800 transition">Filter</button>
        <a href="{{ route('owner.report') }}" class="px-6 py-3 bg-slate-100 text-slate-500 rounded-xl font-bold hover:bg-slate-200 transition">Reset</a>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm p-10 print:p-0 print:shadow-none mx-auto" id="printable-area">
    
    <div class="hidden print:block mb-8 border-b-4 border-slate-900 pb-6">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-6">
                <img src="{{ asset('images/logo-ilin.png') }}" alt="Logo Ilin Catering" class="h-28 w-auto object-contain">
                <div>
                    <h1 class="text-4xl font-black text-slate-900 uppercase tracking-tighter">Ilin Catering</h1>
                    <p class="text-sm text-slate-500 font-bold uppercase tracking-widest mt-1">Layanan Katering & Snack Box</p>
                    <p class="text-[10px] text-slate-400 mt-2">Jl. Abdul Mutalib RT/RW : 006/001 Kel/Desa : Padang, Kec. Bati-Bati</p>
                    <p class="text-[10px] text-slate-400">WhatsApp: 0887-4354-14960</p>
                </div>
            </div>
            <div class="text-right">
                <h2 class="text-xl font-black text-orange-600 uppercase">Laporan Penjualan</h2>
                <p class="text-xs text-slate-500 italic">Periode: {{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->translatedFormat('d M Y') : 'Awal' }} s/d {{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->translatedFormat('d M Y') : 'Sekarang' }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-6 mb-10">
        <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Transaksi</p>
            <p class="text-2xl font-black text-slate-900">{{ $orders->count() ?? 0 }} Pesanan</p>
        </div>
        <div class="p-6 bg-orange-50 rounded-3xl border border-orange-100">
            <p class="text-[10px] font-bold text-orange-400 uppercase tracking-widest">Total Omzet Pendapatan</p>
            <p class="text-2xl font-black text-orange-600">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</p>
        </div>
    </div>

    <table class="w-full mb-10">
        <thead>
            <tr class="border-b-2 border-slate-200 text-left">
                <th class="py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Tgl Transaksi</th>
                <th class="py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Nomor Order</th>
                <th class="py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Nama Pelanggan</th>
                <th class="py-4 text-xs font-bold text-slate-400 uppercase tracking-wider text-right">Nominal</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($orders as $order)
            <tr>
                <td class="py-4 text-sm text-slate-600">{{ $order->created_at->format('d M Y') }}</td>
                <td class="py-4 text-sm font-bold text-slate-900">#{{ $order->order_number }}</td>
                <td class="py-4 text-sm text-slate-600">{{ $order->recipient_name ?? $order->user->name }}</td>
                <td class="py-4 text-sm font-black text-slate-900 text-right">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="py-10 text-center text-slate-400 italic">Tidak ada data transaksi ditemukan.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="border-t-2 border-slate-900">
                <td colspan="3" class="py-4 font-bold text-slate-900 uppercase">Total Keseluruhan</td>
                <td class="py-4 font-black text-xl text-orange-600 text-right">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="hidden print:grid grid-cols-2 gap-20 mt-20">
        <div class="text-center">
            <p class="text-xs text-slate-400 mb-20 uppercase font-bold tracking-widest">Dicetak Oleh,</p>
            <p class="font-black text-slate-900 border-b-2 border-slate-900 inline-block px-4 uppercase">{{ auth()->user()->name ?? 'Admin' }}</p>
            <p class="text-[10px] text-slate-400 uppercase tracking-tighter mt-1">Sistem Ilin Catering</p>
        </div>
        <div class="text-center">
            <p class="text-xs text-slate-400 mb-20 uppercase font-bold tracking-widest">Mengetahui, Owner</p>
            <p class="font-black text-slate-900 border-b-2 border-slate-900 inline-block px-12 uppercase">Ibu Ilin</p>
            <p class="text-[10px] text-slate-400 uppercase tracking-tighter mt-1">Pimpinan Ilin Catering</p>
        </div>
    </div>
</div>

<style>
    @media print {
        /* Sembunyikan navigasi dan tombol */
        nav, aside, .no-print, button { display: none !important; }
        body { background-color: white !important; margin: 0 !important; padding: 0 !important; }
        main { margin-left: 0 !important; padding: 0 !important; width: 100% !important; }
        
        .shadow-sm, .border { border: none !important; box-shadow: none !important; }
        
        .text-slate-900 { color: #0f172a !important; }
        .text-orange-600 { color: #ea580c !important; }
        
        .bg-slate-50 { background-color: #f8fafc !important; -webkit-print-color-adjust: exact; }
        .bg-orange-50 { background-color: #fff7ed !important; -webkit-print-color-adjust: exact; }
    }
    #printable-area { max-width: 900px; }
</style>
@endsection