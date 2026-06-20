<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        return view('pelanggan.keranjang', compact('cart'));
    }

    public function add(Request $request)
    {
        $cart = session()->get('cart', []);
        $nama = $request->nama;

        // Jika menu sudah ada, tambah qty saja
        if(isset($cart[$nama])) {
            $cart[$nama]['qty']++;
        } else {
            // Jika belum ada, buat entry baru dengan nama sebagai Key
            $cart[$nama] = [
                'nama' => $request->nama,
                'harga' => $request->harga,
                'qty' => 1
            ];
        }

        session()->put('cart', $cart);
        
        // 🌟 FIX: Diubah ke back() agar tetap di halaman menu saat klik tambah, plus bonus pop-up sukses
        return redirect()->back()->with('success', 'Menu berhasil ditambahkan ke keranjang!');
    }

    // 🛠️ FUNGSI UPDATE: Menangani tombol + dan - dengan aman
    public function update(Request $request)
    {
        $cart = session()->get('cart');
        $id = $request->id; // ID disini adalah Nama Menu (Key)

        if(isset($cart[$id])) {
            if($request->action == 'increase') {
                $cart[$id]['qty']++;
            } elseif($request->action == 'decrease' && $cart[$id]['qty'] > 1) {
                $cart[$id]['qty']--;
            }
            session()->put('cart', $cart);
        }

        // 🌟 FIX UTAMA: Sekarang pakai back() agar tetap di tempat, dan membawa pesan sukses untuk memicu pop-up toast!
        return redirect()->back()->with('success', 'Jumlah pesanan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $cart = session()->get('cart');

        if(isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return redirect()->back()->with('success', 'Item berhasil dihapus dari keranjang!');
    }
}