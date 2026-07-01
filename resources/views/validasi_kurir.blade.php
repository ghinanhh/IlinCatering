<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validasi Kurir | Ilin Catering</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-slate-900 text-white font-sans min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-slate-800 p-6 rounded-[2.5rem] border border-slate-700 shadow-2xl text-center">
        <div class="w-16 h-16 bg-orange-500/10 text-orange-500 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-4">
            <i class="fa-solid fa-truck-ramp-box"></i>
        </div>
        
        <h2 class="text-xl font-black uppercase tracking-tight">Validasi Hantaran Kurir</h2>
        <p class="text-xs text-slate-400 mt-1">Pesanan: <span class="text-orange-400 font-bold">#{{ $order->order_number }}</span></p>

        @if(session('success'))
            <div class="mt-6 p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-2xl text-xs font-bold uppercase leading-relaxed">
                ✅ {{ session('success') }}
            </div>
        @endif

        {{-- Ringkasan Alamat & Tagihan Pembayaran --}}
        <div class="my-6 bg-slate-850 p-5 rounded-2xl border border-slate-750 text-left space-y-3">
            <div>
                <p class="text-[9px] text-slate-500 font-bold uppercase tracking-wider">Nama Penerima:</p>
                <p class="text-sm font-bold text-white">{{ $order->recipient_name }}</p>
            </div>
            <div>
                <p class="text-[9px] text-slate-500 font-bold uppercase tracking-wider">Alamat Hantaran:</p>
                <p class="text-xs font-semibold text-slate-300 leading-relaxed">{{ $order->address }}</p>
            </div>
            <div class="pt-2 border-t border-slate-750 flex justify-between items-center">
                <div>
                    <p class="text-[9px] text-slate-500 font-bold uppercase tracking-wider">Sisa Tagihan COD:</p>
                    <p class="text-base font-black text-emerald-400">Rp {{ number_format($order->remaining_payment, 0, ',', '.') }}</p>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[9px] font-black uppercase {{ $order->remaining_payment > 0 ? 'bg-amber-500/10 text-amber-400' : 'bg-emerald-500/10 text-emerald-400' }}">
                    {{ $order->remaining_payment > 0 ? 'Tagih COD' : 'Sudah Lunas' }}
                </span>
            </div>
        </div>

        @if(in_array(strtolower($order->status), ['done', 'selesai']))
            <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-2xl text-xs font-black uppercase tracking-wider">
                🎉 Hantaran Ini Sudah Berhasil Diselesaikan
            </div>
        @else
            {{-- FORM UPLOAD KAMERA KURIR --}}
            <form action="{{ route('kurir.validasi.proses', $order->order_number) }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-left">
                @csrf
                
                <div>
                    <label class="block text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-2">Ambil Foto Bukti di Lokasi:</label>
                    <div class="relative bg-slate-750 border-2 border-dashed border-slate-600 rounded-2xl p-5 text-center hover:border-orange-500 transition cursor-pointer group">
                        {{-- Atribut accept="image/*" otomatis memicu Kamera HP Kurir saat disentuh --}}
                        <input type="file" name="bukti_foto" accept="image/*" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        <i class="fa-solid fa-camera text-3xl text-slate-500 group-hover:text-orange-500 mb-2 block transition"></i>
                        <span class="text-xs text-slate-300 font-bold block">Klik untuk Buka Kamera HP</span>
                        <span class="text-[10px] text-slate-500 mt-0.5 block">Ambil foto pesanan bersama penerima</span>
                    </div>
                    @error('bukti_foto')
                        <p class="text-xs text-rose-500 font-semibold mt-2">⚠️ {{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white py-4 rounded-xl font-black text-xs uppercase tracking-widest transition shadow-lg shadow-orange-950/20 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-circle-check text-sm"></i> Konfirmasi Selesai & Lunas
                </button>
            </form>
        @endif
    </div>

</body>
</html>