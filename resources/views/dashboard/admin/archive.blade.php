@extends('layouts.app')

@section('content')
@php
    // 🌟 Mengambil list bulan unik secara dinamis dari data database yang ada
    $availableMonths = $archivedOrders->map(function($order) {
        return \Carbon\Carbon::parse($order->event_date)->locale('id')->isoFormat('MMMM YYYY');
    })->unique();
@endphp

<div class="mb-6 sm:mb-8 flex flex-col sm:flex-row justify-between items-center gap-4 text-center sm:text-left">
    <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Arsip & Riwayat Pesanan 🗄️</h1>
        <p class="text-sm sm:text-base text-slate-500 mt-1">Daftar seluruh rekam jejak riwayat pesanan Ilin Catering yang telah selesai maupun dibatalkan.</p>
    </div>
    
    {{-- Tombol Navigasi Dinamis Multi-Role --}}
    @if(auth()->user()->role === 'admin')
        <a href="{{ route('admin.orders') }}" class="bg-slate-100 text-slate-700 px-5 py-2.5 rounded-2xl font-bold text-sm hover:bg-slate-200 transition flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Pesanan Aktif
        </a>
    @else
        <a href="{{ route('owner.dashboard') }}" class="bg-slate-100 text-slate-700 px-5 py-2.5 rounded-2xl font-bold text-sm hover:bg-slate-200 transition flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
        </a>
    @endif
</div>

{{-- 🌟 SECTION FILTER DROPDOWN BULAN (SEIMBANG KIRI KANAN) --}}
<div class="mb-5 flex justify-between items-center bg-white p-4 rounded-2xl shadow-sm border border-slate-100">
    <label for="monthFilter" class="text-xs sm:text-sm font-black text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
        <i class="fa-solid fa-filter text-orange-500"></i> Pilih Bulan Acara Katering
    </label>
    <select id="monthFilter" class="bg-slate-50 border border-slate-200 text-slate-800 text-xs sm:text-sm font-bold px-4 py-2 rounded-xl outline-none focus:border-orange-500 focus:bg-white transition min-w-[160px]">
        <option value="ALL">✨ Semua Bulan</option>
        @foreach($availableMonths as $month)
            <option value="{{ $month }}">{{ $month }}</option>
        @endforeach
    </select>
</div>

<div class="bg-white rounded-3xl sm:rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[700px]">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="p-4 sm:p-6 text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider">Pelanggan</th>
                    <th class="p-4 sm:p-6 text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider">Rincian</th>
                    <th class="p-4 sm:p-6 text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider">Total Tagihan</th>
                    <th class="p-4 sm:p-6 text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider">Tanggal Acara</th>
                    <th class="p-4 sm:p-6 text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider text-center">Status Akhir</th>
                </tr>
            </thead>
            <tbody id="archiveTableBody" class="divide-y divide-slate-50 text-sm">
                @forelse($archivedOrders as $order)
                <tr class="archive-row hover:bg-slate-50/50 transition" data-month="{{ \Carbon\Carbon::parse($order->event_date)->locale('id')->isoFormat('MMMM YYYY') }}">
                    <td class="p-4 sm:p-6">
                        <p class="font-bold text-slate-900">{{ $order->recipient_name ?? ($order->user->name ?? 'Pelanggan Offline') }}</p>
                        <p class="text-[9px] sm:text-[10px] font-medium text-slate-400 px-2 py-0.5 bg-slate-100 rounded-md inline-block mt-1">
                            #{{ $order->order_number }}
                        </p>
                    </td>

                    <td class="p-4 sm:p-6">
                        <button onclick="openModal('modal-{{ $order->id }}')" class="flex items-center gap-2 text-xs sm:text-sm font-bold text-blue-600 hover:text-blue-700 transition group">
                            <i class="fa-solid fa-circle-info transition group-hover:scale-110"></i>
                            <span class="whitespace-nowrap">Lihat Detail Nota</span>
                        </button>
                    </td>

                    <td class="p-4 sm:p-6">
                        <p class="font-black text-slate-900 whitespace-nowrap">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                    </td>

                    <td class="p-4 sm:p-6 text-slate-600 font-medium">
                        {{ \Carbon\Carbon::parse($order->event_date)->locale('id')->isoFormat('D MMMM YYYY') }}
                    </td>

                    <td class="p-4 sm:p-6">
                        <div class="flex items-center justify-center">
                            @if(in_array(strtolower($order->status), ['batal', 'canceled', 'expired']))
                                <span class="bg-red-100 text-red-700 px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-wider flex items-center gap-1.5">
                                    <i class="fa-solid fa-circle-xmark"></i> Batal
                                </span>
                            @else
                                <span class="bg-green-100 text-green-700 px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-wider flex items-center gap-1.5">
                                    <i class="fa-solid fa-circle-check"></i> Selesai
                                </span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr id="noDataRow">
                    <td colspan="5" class="px-6 py-12 text-center text-slate-400 italic">
                        <i class="fa-solid fa-folder-open text-4xl mb-3 block"></i>
                        Belum ada pesanan yang diarsipkan...
                    </td>
                </tr>
                @endforelse

                <tr id="filterEmptyRow" class="hidden">
                    <td colspan="5" class="px-6 py-12 text-center text-slate-400 italic">
                        <i class="fa-solid fa-calendar-xmark text-4xl mb-3 block text-slate-300"></i>
                        Tidak ada riwayat pesanan pada bulan yang dipilih...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Box Area --}}
@foreach($archivedOrders as $order)
<div id="modal-{{ $order->id }}" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl sm:rounded-[2.5rem] w-full max-w-md p-6 sm:p-8 shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
        <div class="flex justify-between items-center mb-4 sm:mb-6">
            <div>
                <h3 class="text-lg sm:text-xl font-black text-slate-900">Detail Riwayat Pesanan</h3>
                <p class="text-[10px] sm:text-xs text-slate-400 mt-1">ID: #{{ $order->order_number }}</p>
            </div>
            <button onclick="closeModal('modal-{{ $order->id }}')" class="text-slate-400 hover:text-red-500 text-2xl outline-none">&times;</button>
        </div>

        <div class="overflow-y-auto pr-2 custom-scrollbar">
            @if(in_array(strtolower($order->status), ['batal', 'canceled', 'expired']))
                <div class="bg-red-50 p-4 rounded-2xl border border-red-100 mb-4 text-center">
                    <p class="text-xs font-bold text-red-800 uppercase tracking-wide">❌ Pesanan Dibatalkan / Gagal</p>
                </div>
            @else
                <div class="bg-green-50 p-4 rounded-2xl border border-green-100 mb-4 text-center">
                    <p class="text-xs font-bold text-green-800 uppercase tracking-wide">✅ Transaksi Selesai & Lunas 100%</p>
                </div>
            @endif

            <div class="bg-slate-50 p-4 sm:p-5 rounded-2xl sm:rounded-3xl border border-slate-100 mb-4 shadow-inner">
                <p class="text-[9px] sm:text-[10px] font-bold text-slate-400 uppercase mb-2 tracking-widest">Eks-Alamat Penerima:</p>
                <div class="flex items-center gap-2 mb-1">
                    <i class="fa-solid fa-user text-slate-400 text-xs"></i>
                    <p class="text-xs sm:text-sm font-bold text-slate-900">{{ $order->recipient_name }}</p>
                </div>
                <div class="bg-white p-3 rounded-xl border border-slate-100 text-xs text-slate-500 italic">
                    {{ $order->address }}
                </div>
            </div>

            <div class="space-y-3 mb-4">
                <p class="text-[9px] sm:text-[10px] font-bold text-slate-400 uppercase tracking-widest px-1">Menu Terkirim / Dipesan:</p>
                @foreach($order->items as $item)
                <div class="border-b border-slate-100 pb-3 px-1">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-bold text-slate-900 text-xs sm:text-sm">{{ $item->menu->title }}</p>
                            <p class="text-[10px] sm:text-[11px] text-slate-500">{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                        </div>
                        <p class="font-black text-slate-900 text-xs sm:text-sm shrink-0">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</p>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="bg-slate-900 p-5 rounded-2xl text-white space-y-2">
                <div class="flex justify-between items-center text-xs text-slate-400 uppercase font-bold">
                    <span>{{ in_array(strtolower($order->status), ['batal', 'canceled', 'expired']) ? 'Total Nilai Pembatalan' : 'Total Omzet Masuk' }}</span>
                    <span class="text-yellow-400 font-black text-sm">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-slate-100">
                <a href="{{ auth()->user()->role === 'admin' ? route('admin.cetak_nota', $order->id) : route('owner.cetak_nota', $order->id) }}" class="w-full py-3 bg-orange-600 text-white rounded-xl font-bold hover:bg-orange-700 transition flex items-center justify-center gap-2 shadow-md">
                    <i class="fa-solid fa-print"></i> Cetak / Unduh Nota Lunas
                </a>
            </div>

        </div>
    </div>
</div>
@endforeach

<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // LOGIKA UTAMA SINKRONISASI DROPDOWN FILTER (VANILLA JS)
    document.getElementById('monthFilter').addEventListener('change', function() {
        const selectedMonth = this.value;
        const rows = document.querySelectorAll('.archive-row');
        const filterEmptyRow = document.getElementById('filterEmptyRow');
        let visibleCount = 0;

        rows.forEach(row => {
            const rowMonth = row.getAttribute('data-month');
            
            if (selectedMonth === 'ALL' || rowMonth === selectedMonth) {
                row.style.display = ''; // Tampilkan baris
                visibleCount++;
            } else {
                row.style.display = 'none'; // Sembunyikan baris
            }
        });

        // Jika tidak ada data yang cocok dengan bulan terpilih, munculkan baris peringatan kosong
        if (visibleCount === 0 && rows.length > 0) {
            filterEmptyRow.classList.remove('hidden');
        } else {
            filterEmptyRow.classList.add('hidden');
        }
    });
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
</style>
@endsection