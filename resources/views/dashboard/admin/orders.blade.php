@extends('layouts.app')

@section('content')
<div class="p-6 bg-slate-50 min-h-screen">
    <div class="mb-6 sm:mb-8 flex flex-col sm:flex-row justify-between items-center gap-4 text-center sm:text-left">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Kelola Pesanan Masuk 📦</h1>
            <p class="text-sm sm:text-base text-slate-500 mt-1 sm:mt-0">Update status pesanan pelanggan Ilin Catering di sini.</p>
        </div>
        {{-- Tombol Pemicu Pop-up Form Input Manual Pesanan WA --}}
        <button onclick="openModal('modal-manual-order')" class="w-full sm:w-auto bg-slate-900 hover:bg-orange-600 text-white px-5 py-3 rounded-2xl text-xs font-black uppercase tracking-wider transition shadow-sm flex items-center justify-center gap-2 outline-none cursor-pointer">
            <i class="fa-solid fa-square-plus text-sm"></i> Input Pesanan Manual (WA)
        </button>
    </div>

    {{-- Menampilkan Pesan Error Validasi Form Jika Ada Inputan yang Salah --}}
    @if($errors->any())
    <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-2xl mb-6 text-xs font-semibold space-y-1">
        <p class="font-bold">⚠️ Gagal menyimpan pesanan manual, silakan periksa kembali:</p>
        <ul class="list-disc pl-4 space-y-0.5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-6 text-xs font-bold">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white rounded-3xl sm:rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[700px]">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="p-4 sm:p-6 text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider">Pelanggan</th>
                        <th class="p-4 sm:p-6 text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider">Rincian</th>
                        <th class="p-4 sm:p-6 text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider">Total Tagihan</th>
                        <th class="p-4 sm:p-6 text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider">Status Pesanan</th>
                        <th class="p-4 sm:p-6 text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-sm">
                    @foreach($orders as $order)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="p-4 sm:p-6">
                            <p class="font-bold text-slate-900">{{ $order->recipient_name ?? ($order->user->name ?? 'Pelanggan Offline') }}</p>
                            <p class="text-[9px] sm:text-[10px] font-medium text-slate-400 px-2 py-0.5 bg-slate-100 rounded-md inline-block mt-1">
                                #{{ $order->order_number }}
                            </p>
                        </td>

                        <td class="p-4 sm:p-6">
                            <button onclick="openModal('modal-{{ $order->id }}')" class="flex items-center gap-2 text-xs sm:text-sm font-bold text-blue-600 hover:text-blue-700 transition group">
                                <i class="fa-solid fa-circle-info transition group-hover:scale-110"></i>
                                <span class="whitespace-nowrap">Lihat Detail & Jadwal</span>
                            </button>
                        </td>

                        <td class="p-4 sm:p-6">
                            <p class="font-black text-slate-900 whitespace-nowrap">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                        </td>

                        <td class="p-4 sm:p-6">
                            @php
                                $statusClasses = [
                                    'pending'    => 'bg-orange-100 text-orange-600',
                                    'lunas dp'   => 'bg-amber-100 text-amber-700',
                                    'konfirmasi' => 'bg-blue-100 text-blue-600',
                                    'confirmed'  => 'bg-blue-100 text-blue-600', 
                                    'dimasak'    => 'bg-purple-100 text-purple-600',
                                    'cooking'    => 'bg-purple-100 text-purple-600', 
                                    'dikirim'    => 'bg-cyan-100 text-cyan-600',
                                    'shipping'   => 'bg-cyan-100 text-cyan-600', 
                                    'selesai'    => 'bg-green-100 text-green-600',
                                    'done'       => 'bg-green-100 text-green-600', 
                                ];
                            @endphp
                            <span class="px-2 sm:px-3 py-1 rounded-full text-[9px] sm:text-[10px] font-black uppercase tracking-tighter {{ $statusClasses[strtolower($order->status)] ?? 'bg-slate-100 text-slate-600' }}">
                                @if(strtolower($order->status) == 'confirmed') Konfirmasi
                                @elseif(strtolower($order->status) == 'cooking') Dimasak
                                @elseif(strtolower($order->status) == 'shipping') Dikirim
                                @elseif(strtolower($order->status) == 'done') Selesai
                                @else {{ $order->status }}
                                @endif
                            </span>
                        </td>

                        <td class="p-4 sm:p-6">
                            <div class="flex flex-col gap-2 items-center justify-center">
                                <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST" class="flex flex-col sm:flex-row items-center gap-2 justify-center w-full">
                                    @csrf
                                    <select name="status" class="text-[10px] sm:text-xs border-slate-200 rounded-xl focus:ring-orange-500 focus:border-orange-500 py-1.5 pl-2 sm:pl-3 pr-6 sm:pr-8 w-full sm:w-auto outline-none capitalize">
                                        <option value="pending" {{ in_array(strtolower($order->status), ['pending']) ? 'selected' : '' }}>Pending</option>
                                        <option value="lunas dp" {{ in_array(strtolower($order->status), ['lunas dp']) ? 'selected' : '' }}>Lunas DP</option>
                                        <option value="konfirmasi" {{ in_array(strtolower($order->status), ['konfirmasi', 'confirmed']) ? 'selected' : '' }}>Konfirmasi</option>
                                        <option value="dimasak" {{ in_array(strtolower($order->status), ['dimasak', 'cooking']) ? 'selected' : '' }}>Dimasak</option>
                                        <option value="dikirim" {{ in_array(strtolower($order->status), ['dikirim', 'shipping']) ? 'selected' : '' }}>Dikirim</option>
                                        <option value="selesai" {{ in_array(strtolower($order->status), ['selesai', 'done']) ? 'selected' : '' }}>Selesai</option>
                                        <option value="batal" {{ in_array(strtolower($order->status), ['batal', 'canceled']) ? 'selected' : '' }}>Batal</option>
                                    </select>
                                    <button type="submit" class="bg-slate-900 text-white w-full sm:w-auto px-4 py-1.5 rounded-xl hover:bg-orange-600 transition shadow-sm text-[10px] sm:text-xs font-bold">
                                        Update
                                    </button>
                                </form>

                                @if($order->payment_status != 'settlement')
                                    <p class="text-[9px] text-red-500 font-bold tracking-tight text-center">⚠️ Jangan dimasak! DP belum lunas.</p>
                                @endif

                                @if($order->payment_status == 'settlement' && !in_array(strtolower($order->status), ['selesai', 'done']))
                                    <form action="{{ route('admin.orders.completePayment', $order->id) }}" method="POST" class="w-full">
                                        @csrf
                                        <button type="submit" class="w-full bg-green-600 text-white px-3 py-1.5 rounded-xl hover:bg-green-700 transition shadow-sm text-[10px] font-bold whitespace-nowrap">
                                            <i class="fa-solid fa-money-bill-wave mr-1"></i> Tandai Lunas COD (100%)
                                        </button>
                                    </form>
                                @endif

                                {{-- 🌟 TAMBAHAN: Tombol Interaktif Pengintip Bukti Foto Lapangan Kurir --}}
                                @if($order->bukti_foto)
                                    <button type="button" onclick="openModal('modal-photo-{{ $order->id }}')" class="w-full bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-1.5 rounded-xl transition shadow-xs text-[10px] font-black uppercase tracking-wider flex items-center justify-center gap-1.5 whitespace-nowrap">
                                        <i class="fa-solid fa-image text-orange-500"></i> Lihat Bukti Kurir
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </div>
            </table>
        </div>
    </div>
</div>

{{-- ================= POP-UP MODAL 1: FORM INPUT PESANAN OFFLINE MANUAL (WA/TOKO) ================= --}}
<div id="modal-manual-order" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-[2.5rem] w-full max-w-xl p-6 sm:p-8 shadow-2xl overflow-hidden flex flex-col max-h-[95vh]">
        <div class="flex justify-between items-center mb-4 border-b border-slate-100 pb-4">
            <div>
                <h3 class="text-xl font-black text-slate-900">Catat Pesanan Offline (WA)</h3>
                <p class="text-xs text-slate-400 mt-0.5">Entri langsung pesanan dari luar website katering.</p>
            </div>
            <button onclick="closeModal('modal-manual-order')" class="text-slate-400 hover:text-red-500 text-2xl outline-none">&times;</button>
        </div>

        <form action="{{ route('admin.orders.storeManual') }}" method="POST" class="overflow-y-auto pr-2 custom-scrollbar space-y-4 text-xs">
            @csrf
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Pelanggan <span class="text-rose-500">*</span></label>
                    <input type="text" name="recipient_name" required placeholder="Contoh: Ibu Fatimah" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-orange-500 font-medium">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nomor WhatsApp <span class="text-rose-500">*</span></label>
                    <input type="number" name="phone_number" required placeholder="Contoh: 0812345678" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-orange-500 font-medium">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Alamat Lengkap Pengiriman <span class="text-rose-500">*</span></label>
                <textarea name="address" required placeholder="Jl. Raya Asam-Asam No. 12, RT 04..." rows="2" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-orange-500 font-medium"></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Tanggal Acara <span class="text-rose-500">*</span></label>
                    <input type="date" name="event_date" min="{{ date('Y-m-d') }}" required class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-orange-500 font-medium">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Jam Pengiriman <span class="text-rose-500">*</span></label>
                    <input type="time" name="event_time" required class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-orange-500 font-medium">
                </div>
            </div>

            <div class="border-t border-slate-100 pt-3">
                <div class="flex justify-between items-center mb-2">
                    <label class="block font-black text-slate-900 uppercase tracking-wider text-[10px]">Daftar Menu Dipesan <span class="text-rose-500">*</span></label>
                    <button type="button" onclick="addMenuRow()" class="text-[10px] bg-slate-100 hover:bg-orange-50 hover:text-orange-600 text-slate-700 px-2.5 py-1 rounded-lg font-bold transition">
                        + Tambah Menu Lain
                    </button>
                </div>

                <div id="manual-menu-container" class="space-y-3">
                    <div class="flex flex-col sm:flex-row gap-2 items-start sm:items-center bg-slate-50 p-3 rounded-2xl border border-slate-100 relative group">
                        <div class="flex-1 w-full">
                            <select name="menu_ids[]" required class="w-full p-2 bg-white border border-slate-200 rounded-xl font-medium outline-none">
                                <option value="" disabled selected>-- Pilih Menu Katering --</option>
                                @foreach($menus as $menu)
                                    <option value="{{ $menu->id }}">[{{ ucfirst($menu->category) }}] {{ $menu->title }} - Rp{{ number_format($menu->price, 0, ',', '.') }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-full sm:w-20">
                            <input type="number" name="quantities[]" min="1" value="1" required class="w-full p-2 bg-white border border-slate-200 rounded-xl text-center font-bold outline-none" title="Jumlah Porsi">
                        </div>
                        <div class="w-full flex-1">
                            <input type="text" name="notes[]" placeholder="Catatan (opsional)" class="w-full p-2 bg-white border border-slate-200 rounded-xl outline-none">
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100/80">
                <label class="block font-black text-slate-800 uppercase tracking-wider text-[10px] mb-2">
                    <i class="fa-solid fa-money-bill-transfer text-orange-500 mr-1"></i> Verifikasi Pembayaran Awal <span class="text-rose-500">*</span>
                </label>
                <select name="payment_status_input" required class="w-full p-2.5 bg-white border border-slate-200 rounded-xl font-medium outline-none text-xs focus:ring-2 focus:ring-orange-500">
                    <option value="belum_bayar">❌ Belum Bayar DP (Status: Hutang / Booking Slot)</option>
                    <option value="sudah_dp" selected>✅ Sah Sudah Bayar DP 30% (Uang Cash Diterima / TF Dicek)</option>
                </select>
                <p class="text-[9px] text-slate-400 mt-1 italic">Pilihan ini menjadi bukti audit digital untuk membedakan pesanan rekanan yang sudah bayar vs piutang.</p>
            </div>

            <div class="pt-4 border-t border-slate-100 flex gap-3">
                <button type="submit" class="flex-1 py-3 bg-orange-600 text-white font-bold rounded-2xl hover:bg-orange-700 transition shadow-md">Simpan Pesanan</button>
                <button type="button" onclick="closeModal('modal-manual-order')" class="px-5 py-3 bg-slate-100 text-slate-600 font-bold rounded-2xl hover:bg-slate-200 transition">Batal</button>
            </div>
        </form>
    </div>
</div>

{{-- POP-UP MODAL 2: DETAIL DAN JADWAL MASAK MASING-MASING NOTA --}}
@foreach($orders as $order)
<div id="modal-{{ $order->id }}" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl sm:rounded-[2.5rem] w-full max-w-md p-6 sm:p-8 shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
        <div class="flex justify-between items-center mb-4 sm:mb-6">
            <div>
                <h3 class="text-lg sm:text-xl font-black text-slate-900">Detail Pesanan</h3>
                <p class="text-[10px] sm:text-xs text-slate-400 mt-1">ID: #{{ $order->order_number }}</p>
            </div>
            <button onclick="closeModal('modal-{{ $order->id }}')" class="text-slate-400 hover:text-red-500 text-2xl outline-none">&times;</button>
        </div>

        <div class="overflow-y-auto pr-2 custom-scrollbar">
            <div class="bg-blue-50 p-3 sm:p-4 rounded-2xl sm:rounded-3xl border border-blue-100 mb-4">
                <p class="text-[9px] sm:text-[10px] font-bold text-blue-400 uppercase mb-2 tracking-widest text-center">Jadwal Pengantaran Acara</p>
                <div class="flex justify-around items-center">
                    <div class="text-center">
                        <i class="fa-solid fa-calendar-check text-blue-600 mb-1 text-sm sm:text-base"></i>
                        <p class="text-xs sm:text-sm font-bold text-slate-900">{{ \Carbon\Carbon::parse($order->event_date)->translatedFormat('d F Y') ?? 'Belum Set' }}</p>
                    </div>
                    <div class="w-[1px] h-8 bg-blue-200"></div>
                    <div class="text-center">
                        <i class="fa-solid fa-clock text-blue-600 mb-1 text-sm sm:text-base"></i>
                        <p class="text-xs sm:text-sm font-bold text-slate-900">{{ $order->event_time ?? '--:--' }} WITA</p>
                    </div>
                </div>
            </div>

            <div class="bg-slate-50 p-4 sm:p-5 rounded-2xl sm:rounded-3xl border border-slate-100 mb-4 sm:mb-6 shadow-inner">
                <p class="text-[9px] sm:text-[10px] font-bold text-slate-400 uppercase mb-2 tracking-widest">Alamat Penerima:</p>
                <div class="flex items-center gap-2 mb-1">
                    <i class="fa-solid fa-user text-slate-400 text-[10px] sm:text-xs"></i>
                    <p class="text-xs sm:text-sm font-bold text-slate-900">{{ $order->recipient_name }}</p>
                </div>
                <div class="flex items-center gap-2 mb-3">
                    <i class="fa-brands fa-whatsapp text-green-500 text-[10px] sm:text-xs"></i>
                    <p class="text-xs sm:text-sm text-slate-600 font-medium">{{ $order->phone_number }}</p>
                </div>
                <div class="bg-white p-3 rounded-xl sm:rounded-2xl border border-slate-100 text-[10px] sm:text-[11px] text-slate-500 italic leading-relaxed">
                    <i class="fa-solid fa-location-dot text-orange-400 mr-1"></i>
                    {{ $order->address }}
                </div>
            </div>

            <div class="space-y-3 sm:space-y-4 mb-4 sm:mb-6">
                <p class="text-[9px] sm:text-[10px] font-bold text-slate-400 uppercase tracking-widest px-1">Menu Dipesan:</p>
                @foreach($order->items as $item)
                <div class="border-b border-slate-100 pb-3 sm:pb-4 px-1">
                    <div class="flex justify-between items-start">
                        <div class="pr-2">
                            <p class="font-bold text-slate-900 text-xs sm:text-sm">{{ $item->menu->title }}</p>
                            <p class="text-[10px] sm:text-[11px] text-slate-500">{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                        </div>
                        <p class="font-black text-slate-900 text-xs sm:text-sm shrink-0">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</p>
                    </div>

                    @if($item->notes)
                    <div class="mt-2 bg-orange-50 p-2 sm:p-3 rounded-lg sm:rounded-xl border border-orange-100 flex items-start gap-2">
                        <i class="fa-solid fa-comment-dots text-orange-400 mt-1 text-[8px] sm:text-[10px]"></i>
                        <div>
                            <p class="text-[8px] sm:text-[9px] font-bold text-orange-800 uppercase italic">Catatan:</p>
                            <p class="text-[10px] sm:text-[11px] text-orange-700 leading-tight">"{{ $item->notes }}"</p>
                        </div>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>

            <div class="bg-orange-600 p-5 sm:p-6 rounded-2xl sm:rounded-3xl shadow-lg shadow-orange-100 space-y-2 sm:space-y-3 mb-2">
                <div class="flex justify-between items-center text-[9px] sm:text-[10px] text-orange-200 uppercase font-bold tracking-widest">
                    <span>Total Pembayaran</span>
                    <span class="text-white">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center text-[10px] sm:text-xs text-white pt-2 border-t border-orange-500">
                    <span class="font-bold uppercase tracking-tighter">Wajib DP (30%)</span>
                    <span class="text-base sm:text-lg font-black text-yellow-300">Rp {{ number_format($order->total_price * 0.3, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center text-[9px] sm:text-[10px] text-orange-100 font-medium">
                    <span>Sisa Pelunasan (COD)</span>
                    <span class="font-bold {{ $order->remaining_payment == 0 ? 'line-through text-orange-300' : '' }}">
                        Rp {{ number_format($order->remaining_payment, 0, ',', '.') }}
                    </span>
                </div>
                @if($order->remaining_payment == 0)
                <div class="text-center pt-1">
                    <span class="bg-green-500 text-white px-2 py-0.5 rounded-lg text-[9px] font-black uppercase">Lunas 100% (COD Berhasil)</span>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endforeach

{{-- ================= 🌟 POP-UP MODAL 3: INJEKSI KHUSUS PREVIEW FOTO BUKTI HANTARAN LAPANGAN KURIR ================= --}}
@foreach($orders as $order)
    @if($order->bukti_foto)
    <div id="modal-photo-{{ $order->id }}" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-[2.5rem] w-full max-w-sm p-6 shadow-2xl relative text-center flex flex-col max-h-[90vh]">
            <div class="flex justify-between items-center mb-4 border-b border-slate-100 pb-3">
                <h4 class="font-black text-slate-900 text-sm uppercase tracking-tight flex items-center gap-1.5"><i class="fa-solid fa-camera text-orange-500"></i> Dokumentasi Lapangan</h4>
                <button onclick="closeModal('modal-photo-{{ $order->id }}')" class="text-slate-400 hover:text-red-500 text-2xl outline-none">&times;</button>
            </div>
            <div class="overflow-y-auto mb-4">
                <img src="{{ asset('storage/' . $order->bukti_foto) }}" alt="Foto Bukti Lapangan" class="w-full h-80 object-cover rounded-2xl border border-slate-100 shadow-inner">
            </div>
            <button type="button" onclick="closeModal('modal-photo-{{ $order->id }}')" class="w-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs py-3 rounded-xl transition">
                Tutup Jendela Bukti
            </button>
        </div>
    </div>
    @endif
@endforeach

{{-- FIX KOTAK BANK MENU --}}
<select class="hidden" id="menu-options-bank">
    @foreach($menus as $menu)
        <option value="{{ $menu->id }}">[{{ ucfirst($menu->category) }}] {{ $menu->title }} - Rp{{ number_format($menu->price, 0, ',', '.') }}</option>
    @endforeach
</select>

<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function addMenuRow() {
        const container = document.getElementById('manual-menu-container');
        const bankHtml = document.getElementById('menu-options-bank').innerHTML;
        
        const newRow = document.createElement('div');
        newRow.className = "flex flex-col sm:flex-row gap-2 items-start sm:items-center bg-slate-50 p-3 rounded-2xl border border-slate-100 relative group animate-fade-in";
        
        newRow.innerHTML = `
            <div class="flex-1 w-full">
                <select name="menu_ids[]" required class="w-full p-2 bg-white border border-slate-200 rounded-xl font-medium outline-none">
                    <option value="" disabled selected>-- Pilih Menu Katering --</option>
                    ` + bankHtml + `
                </select>
            </div>
            <div class="w-full sm:w-20">
                <input type="number" name="quantities[]" min="1" value="1" required class="w-full p-2 bg-white border border-slate-200 rounded-xl text-center font-bold outline-none">
            </div>
            <div class="w-full flex-1 flex items-center gap-2">
                <input type="text" name="notes[]" placeholder="Catatan (opsional)" class="w-full p-2 bg-white border border-slate-200 rounded-xl outline-none">
                <button type="button" onclick="this.closest('.group').remove()" class="text-red-500 hover:text-red-700 font-bold px-2 text-sm outline-none cursor-pointer">
                    &times;
                </button>
            </div>
        `;
        container.appendChild(newRow);
    }
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
</style>
@endsection