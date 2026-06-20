@extends('layouts.app')

@section('content')
<div class="p-6 max-w-2xl mx-auto">
    <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100">
        <h3 class="text-2xl font-bold mb-6">Edit Menu: {{ $menu->title }}</h3>
        
        <form action="{{ route('admin.menu.update', $menu->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Nama Menu <span class="text-rose-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $menu->title) }}" class="w-full px-4 py-2 bg-slate-50 border @error('title') border-red-500 @else border-slate-200 @enderror rounded-xl focus:ring-2 focus:ring-orange-500 outline-none" required>
                
                @error('title')
                    <span class="text-xs text-red-500 mt-1 block font-medium">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Harga (Rp) <span class="text-rose-500">*</span></label>
                {{-- 🌟 FIX: Mengubah type ke text agar bisa menampung format titik otomatis --}}
                <input 
                    type="text" 
                    name="price" 
                    id="harga_menu" 
                    value="{{ old('price', $menu->price) }}" 
                    class="w-full px-4 py-2 bg-slate-50 border @error('price') border-red-500 @else border-slate-200 @enderror rounded-xl focus:ring-2 focus:ring-orange-500 outline-none font-semibold text-slate-800" 
                    placeholder="contoh : 60000"
                    required
                >
                
                @error('price')
                    <span class="text-xs text-red-500 mt-1 block font-medium">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Kategori <span class="text-rose-500">*</span></label>
                <select name="category" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none">
                    <option value="box" {{ old('category', $menu->category) == 'box' ? 'selected' : '' }}>Nasi Box</option>
                    <option value="prasmanan" {{ old('category', $menu->category) == 'prasmanan' ? 'selected' : '' }}>Prasmanan</option>
                    <option value="snack" {{ old('category', $menu->category) == 'snack' ? 'selected' : '' }}>Snack Box</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label>
                <textarea name="description" rows="3" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none">{{ old('description', $menu->description) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Foto Baru (Kosongkan jika tidak ganti)</label>
                <input type="file" name="image" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                
                @error('image')
                    <span class="text-xs text-red-500 mt-1 block font-medium">{{ $message }}</span>
                @enderror
                
                <p class="mt-2 text-xs text-slate-400">Foto saat ini:</p>
                <img src="{{ asset('storage/' . $menu->image) }}" class="w-20 h-20 rounded-lg object-cover mt-1">
            </div>

            <div class="flex gap-3 pt-4">
                <button type="submit" class="flex-1 bg-orange-600 text-white py-3 rounded-xl font-bold hover:bg-orange-700 transition">Simpan Perubahan</button>
                <a href="{{ route('admin.menu') }}" class="px-6 py-3 bg-slate-100 text-slate-600 rounded-xl font-bold hover:bg-slate-200 transition">Batal</a>
            </div>
        </form>
    </div>
</div>

{{-- 🌟 JAVASCRIPT: Otomatis memformat input harga dengan titik saat halaman dimuat dan saat diketik --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const hargaInput = document.getElementById('harga_menu');
        const form = hargaInput.closest('form');

        // Format otomatis saat halaman pertama kali dibuka (mengambil data lama dari database)
        if (hargaInput.value) {
            hargaInput.value = formatRupiah(hargaInput.value);
        }

        // Trigger saat admin mengetik ulang harga baru di kolom harga
        hargaInput.addEventListener('input', function () {
            this.value = formatRupiah(this.value);
        });

        // Bersihkan titik murni sebelum form dikirim terbang ke backend MenuController
        form.addEventListener('submit', function () {
            hargaInput.value = hargaInput.value.replace(/\./g, '');
        });

        // Fungsi pembantu menambahkan titik setiap kelipatan 3 digit angka
        function formatRupiah(angka) {
            let nomorMurni = angka.toString().replace(/\D/g, '');
            return nomorMurni.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }
    });
</script>
@endsection