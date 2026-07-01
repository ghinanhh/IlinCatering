<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class KurirController extends Controller
{
    /**
     * Menampilkan halaman validasi hantaran kurir lapangan (Tanpa Login / Publik)
     */
    public function halamanValidasi($order_number)
    {
        // Cari pesanan katering berdasarkan nomor order unik yang diklik dari WhatsApp
        $order = Order::where('order_number', $order_number)->firstOrFail();

        return view('validasi_kurir', compact('order'));
    }

    /**
     * Memproses upload foto dari kamera HP kurir & menyelesaikan sisa tagihan COD 70%
     */
    public function prosesValidasi(Request $request, $order_number)
    {
        $order = Order::where('order_number', $order_number)->firstOrFail();

        // Validasi file gambar yang di-upload lewat kamera HP kurir
        $request->validate([
            'bukti_foto' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Batasi maksimal ukuran file 2MB
        ], [
            'bukti_foto.required' => 'Wajib mengambil foto bukti hantaran langsung dari kamera HP!',
            'bukti_foto.image'    => 'File wajib berupa gambar atau foto yang valid.',
            'bukti_foto.mimes'    => 'Format foto hantaran harus berupa jpeg, png, atau jpg.',
            'bukti_foto.max'      => 'Ukuran foto terlalu besar! Maksimal berukuran 2MB.',
        ]);

        // 🌟 FIX UTAMA: Satukan seluruh data pembaruan finansial & status ke dalam satu array
        $updateData = [
            'remaining_payment' => 0,
            'status'            => 'done', // Otomatis pesanan selesai (sesuaikan 'done' atau 'selesai' pada DB-mu)
            'payment_status'    => 'settlement' // Status pembayaran otomatis lunas cash di lapangan
        ];

        // Menyimpan file foto fisik ke dalam folder: storage/app/public/bukti_hantaran
        if ($request->hasFile('bukti_foto')) {
            $file = $request->file('bukti_foto');
            $path = $file->store('bukti_hantaran', 'public'); 
            
            // Masukkan path folder penyimpanan gambar ke dalam array update data
            $updateData['bukti_foto'] = $path;
        }

        // Eksekusi pembaruan serentak ke database agar sinkronisasi data terkunci permanen
        $order->update($updateData);

        return redirect()->back()->with('success', 'Hantaran sukses divalidasi! Terima kasih kurir atas kerja kerasnya hari ini. ✨');
    }
}