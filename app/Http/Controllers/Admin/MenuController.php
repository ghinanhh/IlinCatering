<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; 
use Illuminate\Support\Str; 

class MenuController extends Controller
{
    /**
     * Menampilkan daftar menu di halaman Admin (DENGAN FITUR SEARCH DAN PEMISAH ARSIP)
     */
    public function index(Request $request) 
    {
        // Ambil kata kunci pencarian dari input nama "search"
        $search = $request->input('search');

        // Ambil data menu Aktif (is_active = true) dengan filter search jika ada
        $activeMenus = Menu::where('is_active', true)
            ->when($search, function ($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('category', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get();

        // Ambil data menu Diarsipkan (is_active = false) dengan filter search jika ada
        $archivedMenus = Menu::where('is_active', false)
            ->when($search, function ($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('category', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get();
        
        // Kirim semua variabel ke view admin (Menggunakan nama 'menus' agar keselarasan kode lama terjaga)
        $menus = Menu::when($search, function ($query, $search) {
            return $query->where('title', 'like', "%{$search}%")
                         ->orWhere('category', 'like', "%{$search}%");
        })->latest()->get();

        return view('dashboard.admin.menu', compact('menus', 'activeMenus', 'archivedMenus'));
    }

    /**
     * FUNGSI BARU: Mengubah status aktif/nonaktif menu (Soft Delete / Arsip)
     */
    public function toggleArchive($id)
    {
        $menu = Menu::findOrFail($id);
        
        // Membalikkan nilai status boolean (true jadi false, false jadi true)
        $menu->is_active = !$menu->is_active;
        $menu->save();

        $statusText = $menu->is_active ? 'diaktifkan kembali dan siap dijual!' : 'berhasil diarsipkan dari daftar menu aktif!';
        return redirect()->back()->with('success', 'Menu "' . $menu->title . '" ' . $statusText);
    }

    /**
     * Menyimpan menu baru ke database
     */
    public function store(Request $request)
    {
        // 1. Validasi Input Super Ketat (Nama Menu Huruf Depan & Harga Minimal Rp 500)
        $request->validate([
            'title'       => 'required|string|min:3|max:255|regex:/^[a-zA-Z]/|unique:menus,title', 
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:500', 
            'category'    => 'required|in:box,prasmanan,snack',
            'stock'       => 'nullable|integer|min:0', 
            'image'       => 'required|image|mimes:jpeg,png,jpg|max:10240', 
        ], [
            'title.unique' => 'Nama menu sudah digunakan! Silakan gunakan nama menu yang lain.',
            'title.min'    => 'Nama menu terlalu pendek! Minimal harus 3 karakter.',
            'title.regex'  => 'Nama menu tidak valid! Wajib diawali dengan huruf (tidak boleh hanya angka atau angka di depan).',
            'price.min'    => 'Harga menu tidak valid! Harga minimal pemesanan harus Rp 500.', 
            'stock.min'    => 'Stok menu tidak valid! Stok tidak boleh bernilai negatif.',
        ]);

        // 2. Proses Upload Gambar + Mesin Kompresor Otomatis (SUDAH AKTIF)
        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . Str::random(5) . '.jpg'; 
            
            $destinationPath = public_path('storage/menus');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            // --- PROSES KOMPRESOR FOTO MENU (NATIVE PHP GD) ---
            $imageSource = imagecreatefromstring(file_get_contents($file));
            
            // Simpan gambar dengan tingkat kualitas 60% (Sangat ringan)
            imagejpeg($imageSource, $destinationPath . '/' . $filename, 60);
            imagedestroy($imageSource); // Bebaskan memori server
            // --------------------------------------------------

            $imagePath = 'menus/' . $filename;
        }

        // 3. Simpan ke Database
        Menu::create([
            'title'       => $request->title,
            'description' => $request->description ?? '-',
            'price'       => $request->price,
            'category'    => $request->category,
            'stock'       => $request->stock ?? 0, 
            'image'       => $imagePath,
            'is_active'   => true, // Otomatis aktif saat pertama dibuat
        ]);

        return redirect()->back()->with('success', 'Menu baru berhasil ditambahkan dan dioptimasi kecepatan lokalnya!');
    }

    /**
     * Menghapus menu dengan Proteksi Riwayat Transaksi (UAT & Integritas Data)
     */
    public function destroy($id)
    {
        try {
            $menu = Menu::findOrFail($id);

            // Ambil path gambar untuk cadangan penghapusan fisik
            $imagePath = $menu->image;

            // Eksekusi hapus di database terlebih dahulu
            $menu->delete();

            // Jika database sukses terhapus, baru file gambar fisiknya ikut dihapus
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            return redirect()->back()->with('success', 'Menu berhasil dihapus permanen!');

        } catch (\Exception $e) {
            if ($e->getCode() === '23000' || str_contains($e->getMessage(), '23000')) {
                return redirect()->back()->with('error', 'Menu ini tidak bisa dihapus karena memiliki riwayat pesanan pelanggan. Sistem mengunci tindakan ini secara otomatis demi menjaga keakuratan laporan keuangan Owner dan validitas nota riwayat belanja pelanggan!');
            }
            
            return redirect()->back()->with('error', 'Gagal menghapus menu: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan halaman edit menu
     */
    public function edit($id)
    {
        $menu = Menu::findOrFail($id);
        return view('dashboard.admin.edit-menu', compact('menu'));
    }

    /**
     * Memperbarui data menu
     */
    public function update(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);

        // Validasi Ketat Juga Diterapkan Saat Edit Data
        $request->validate([
            'title'       => 'required|string|min:3|max:255|regex:/^[a-zA-Z]/|unique:menus,title,' . $id, 
            'price'       => 'required|numeric|min:500', 
            'category'    => 'required|in:box,prasmanan,snack',
            'stock'       => 'nullable|integer|min:0', 
            'image'       => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
        ], [
            'title.unique' => 'Nama menu sudah digunakan! Silakan gunakan nama menu yang lain.',
            'title.min'    => 'Nama menu terlalu pendek! Minimal harus 3 karakter.',
            'title.regex'  => 'Nama menu tidak valid! Wajib diawali dengan huruf (tidak boleh hanya angka atau angka di depan).',
            'price.min'    => 'Harga menu tidak valid! Harga minimal pemesanan harus Rp 500.', 
            'stock.min'    => 'Stok menu tidak valid! Stok tidak boleh bernilai negatif.',
        ]);

        if ($request->hasFile('image')) {
            if ($menu->image) {
                Storage::disk('public')->delete($menu->image);
            }
            
            $file = $request->file('image');
            $filename = time() . '_' . Str::random(5) . '.jpg';
            
            $destinationPath = public_path('storage/menus');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            // --- PROSES KOMPRESOR FOTO UPDATE MENU (NATIVE PHP GD) ---
            $imageSource = imagecreatefromstring(file_get_contents($file));
            imagejpeg($imageSource, $destinationPath . '/' . $filename, 60);
            imagedestroy($imageSource);
            // ---------------------------------------------------------

            $imagePath = 'menus/' . $filename;
            $menu->image = $imagePath;
        }

        $menu->update([
            'title'       => $request->title,
            'description' => $request->description ?? '-',
            'price'       => $request->price,
            'category'    => $request->category,
            'stock'       => $request->stock ?? 0, 
        ]);
        
        $menu->save();

        return redirect()->route('admin.menu')->with('success', 'Menu berhasil diperbarui dan dioptimasi!');
    }
}