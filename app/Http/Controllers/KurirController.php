<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use App\Models\User;

class KurirController extends Controller
{
    /**
     * Menampilkan halaman validasi foto untuk kurir (Tanpa Login)
     */
    public function halamanValidasi($order_number)
    {
        // Cari pesanan berdasarkan nomor order unik yang diklik dari WA
        $order = Order::where('order_number', $order_number)->firstOrFail();

        return view('validasi_kurir', compact('order'));
    }

    /**
     * Memproses upload foto dari kamera HP kurir & menyelesaikan pesanan
     */
    public function prosesValidasi(Request $request, $order_number)
    {
        $order = Order::where('order_number', $order_number)->firstOrFail();

        $request->validate([
            'bukti_foto' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Maksimal 2MB
        ], [
            'bukti_foto.required' => 'Wajib mengambil foto bukti hantaran langsung dari kamera!',
            'bukti_foto.image' => 'File harus berupa gambar/foto yang valid.',
        ]);

        // Proses simpan gambar ke folder: public/storage/bukti_hantaran
        if ($request->hasFile('bukti_foto')) {
            $file = $request->file('bukti_foto');
            $path = $file->store('bukti_hantaran', 'public'); 
            $order->bukti_foto = $path;
        }

        // Otomatis ubah status pesanan menjadi selesai dan lunasi sisa COD 70%
        $order->update([
            'remaining_payment' => 0,
            'status' => 'done', // Sesuaikan dengan kata 'done' atau 'selesai' di sistemmu
            'payment_status' => 'settlement'
        ]);

        return redirect()->back()->with('success', 'Hantaran sukses divalidasi! Terima kasih kurir atas kerja kerasnya hari ini. ✨');
    }
}