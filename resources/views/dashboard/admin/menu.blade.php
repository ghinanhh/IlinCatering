@extends('layouts.app')

@section('content')
@php
    // 🌟 KABEL PENYELARAS KATEGORI: Mengubah teks database murni menjadi lebih formal tanpa emoji
    $categoryLabels = [
        'box' => 'Nasi Box',
        'prasmanan' => 'Prasmanan',
        'snack' => 'Snack Box'
    ];
@endphp

<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Kelola Menu Catering</h2>
        <span class="text-sm text-slate-500">Total Keseluruhan: {{ $menus->count() }} Menu</span>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-6 text-xs font-bold">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-6 text-xs font-bold">
        {{ session('error') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 h-fit">
            <h3 class="text-lg font-bold mb-4">Tambah Menu Baru</h3>
            <form action="{{ route('admin.menu.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nama Menu <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" class="w-full px-4 py-2 bg-slate-50 border @error('title') border-red-500 @else border-slate-200 @enderror rounded-xl focus:ring-2 focus:ring-orange-500 outline-none" required>
                    
                    @error('title')
                        <span class="text-xs text-red-500 mt-1 block font-medium">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Harga (Rp) <span class="text-rose-500">*</span></label>
                    <input 
                        type="text" 
                        name="price" 
                        id="harga_menu" 
                        value="{{ old('price') }}" 
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
                    <select name="category" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-orange-500 outline-none">
                        <option value="box" {{ old('category') == 'box' ? 'selected' : '' }}>Nasi Box</option>
                        <option value="prasmanan" {{ old('category') == 'prasmanan' ? 'selected' : '' }}>Prasmanan</option>
                        <option value="snack" {{ old('category') == 'snack' ? 'selected' : '' }}>Snack Box</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-orange-500 outline-none">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Foto Menu <span class="text-rose-500">*</span></label>
                    <input type="file" name="image" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100" required>
                    @error('image')
                        <span class="text-xs text-red-500 mt-1 block font-medium">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="w-full bg-orange-600 text-white py-3 rounded-xl font-bold hover:bg-orange-700 transition">
                    Simpan Menu
                </button>
            </form>
        </div>

        <div class="lg:col-span-2 bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden p-6">
            <form action="{{ route('admin.menu') }}" method="GET" class="mb-6">
                <div class="relative flex items-center gap-2">
                    <div class="relative flex-1">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Cari nama menu atau kategori (box, snack, prasmanan)..." 
                               class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-orange-500 transition text-sm">
                    </div>
                    <button type="submit" class="px-6 py-2.5 bg-slate-800 text-white rounded-2xl font-bold hover:bg-slate-700 transition shadow-sm">
                        Cari
                    </button>
                    @if(request('search'))
                        <a href="{{ route('admin.menu') }}" class="px-4 py-2.5 bg-slate-200 text-slate-600 rounded-2xl font-bold hover:bg-slate-300 transition text-sm">
                            Reset
                        </a>
                    @endif
                </div>
            </form>

            {{-- 🌟 REVISI UTAMA: Sistem Tab Pengalih Navigasi Menu Aktif vs Arsip --}}
            <div class="flex gap-4 border-b border-slate-100 pb-3 mb-4 text-xs font-black uppercase tracking-wider">
                <button onclick="switchMenuTab('aktif')" id="btn-tab-aktif" class="text-orange-600 border-b-2 border-orange-600 pb-2 outline-none cursor-pointer">
                    🟢 Menu Aktif Dijual ({{ $activeMenus->count() }})
                </button>
                <button onclick="switchMenuTab('arsip')" id="btn-tab-arsip" class="text-slate-400 pb-2 outline-none cursor-pointer hover:text-slate-600">
                    📦 Menu Diarsipkan ({{ $archivedMenus->count() }})
                </button>
            </div>

            {{-- PANEL TAB 1: MENU AKTIF --}}
            <div id="panel-menu-aktif" class="overflow-x-auto border border-slate-100 rounded-2xl">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-sm font-bold text-slate-600">Menu</th>
                            <th class="px-6 py-4 text-sm font-bold text-slate-600">Kategori</th>
                            <th class="px-6 py-4 text-sm font-bold text-slate-600">Harga</th>
                            <th class="px-6 py-4 text-sm font-bold text-slate-600 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($activeMenus as $item) 
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ asset('storage/' . $item->image) }}" class="w-12 h-12 rounded-lg object-cover">
                                    <span class="font-medium text-slate-800">{{ $item->title }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">
                                <span class="bg-slate-100 px-3 py-1 rounded-full text-xs font-semibold text-slate-700">
                                    {{ $categoryLabels[$item->category] ?? ucfirst($item->category) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-slate-900">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-3">
                                    <a href="{{ route('admin.menu.edit', $item->id) }}" class="text-blue-500 hover:text-blue-700 transition" title="Edit Menu">
                                        <i class="fa-solid fa-pen-to-square text-base"></i>
                                    </a>

                                    {{-- Tombol Pindah ke Tab Arsip (Soft Delete) --}}
                                    <form action="{{ route('admin.menu.toggleArchive', $item->id) }}" method="POST" onsubmit="return confirm('Arsip menu ini dari daftar jual?')">
                                        @csrf
                                        <button type="submit" class="text-amber-500 hover:text-amber-600 transition" title="Arsipkan Menu">
                                            <i class="fa-solid fa-box-archive text-base"></i>
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.menu.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus permanen menu ini? Tindakan ini berbahaya!')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-600 transition" title="Hapus Permanen">
                                            <i class="fa-solid fa-trash text-base"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-slate-400 italic">
                                <i class="fa-solid fa-box-open text-4xl mb-3 block"></i>
                                Tidak ada menu aktif yang tersedia...
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PANEL TAB 2: MENU DIARSIPKAN --}}
            <div id="panel-menu-arsip" class="overflow-x-auto border border-slate-100 rounded-2xl hidden">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-sm font-bold text-slate-600">Menu</th>
                            <th class="px-6 py-4 text-sm font-bold text-slate-600">Kategori</th>
                            <th class="px-6 py-4 text-sm font-bold text-slate-600">Harga</th>
                            <th class="px-6 py-4 text-sm font-bold text-slate-600 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-slate-50/50">
                        @forelse($archivedMenus as $item) 
                        <tr class="hover:bg-slate-100 transition opacity-80">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ asset('storage/' . $item->image) }}" class="w-12 h-12 rounded-lg object-cover grayscale">
                                    <span class="font-medium text-slate-500 line-through">{{ $item->title }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500">
                                <span class="bg-slate-200 px-3 py-1 rounded-full text-xs font-semibold text-slate-600">
                                    {{ $categoryLabels[$item->category] ?? ucfirst($item->category) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-slate-500">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-4">
                                    {{-- Tombol Mengembalikan Status Menu Menjadi Aktif Kembali --}}
                                    <form action="{{ route('admin.menu.toggleArchive', $item->id) }}" method="POST" onsubmit="return confirm('Aktifkan dan jual kembali menu ini?')">
                                        @csrf
                                        <button type="submit" class="bg-emerald-600 text-white px-3 py-1.5 rounded-xl font-bold text-[10px] uppercase hover:bg-emerald-700 transition shadow-xs flex items-center gap-1 cursor-pointer">
                                            <i class="fa-solid fa-trash-arrow-up"></i> Tampilkan Lagi
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.menu.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus permanen menu dari arsip?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-600 transition" title="Hapus Permanen">
                                            <i class="fa-solid fa-trash text-base"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-slate-400 italic">
                                <i class="fa-solid fa-folder-open text-4xl mb-3 block"></i>
                                Gudang arsip kosong...
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const hargaInput = document.getElementById('harga_menu');
        const form = hargaInput.closest('form');

        if (hargaInput.value) {
            hargaInput.value = formatRupiah(hargaInput.value);
        }

        hargaInput.addEventListener('input', function () {
            this.value = formatRupiah(this.value);
        });

        form.addEventListener('submit', function () {
            hargaInput.value = hargaInput.value.replace(/\./g, '');
        });

        function formatRupiah(angka) {
            let nomorMurni = angka.replace(/\D/g, '');
            return nomorMurni.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }
    });

    // 🌟 JAVASCRIPT NEW FUNCTION: Saklar Pengendali Tampilan Tab Arsip & Aktif secara instan
    function switchMenuTab(type) {
        const btnAktif = document.getElementById('btn-tab-aktif');
        const btnArsip = document.getElementById('btn-tab-arsip');
        const panelAktif = document.getElementById('panel-menu-aktif');
        const panelArsip = document.getElementById('panel-menu-arsip');

        if (type === 'aktif') {
            btnAktif.className = "text-orange-600 border-b-2 border-orange-600 pb-2 outline-none cursor-pointer";
            btnArsip.className = "text-slate-400 pb-2 outline-none cursor-pointer hover:text-slate-600";
            panelAktif.classList.remove('hidden');
            panelArsip.classList.add('hidden');
        } else {
            btnArsip.className = "text-orange-600 border-b-2 border-orange-600 pb-2 outline-none cursor-pointer";
            btnAktif.className = "text-slate-400 pb-2 outline-none cursor-pointer hover:text-slate-600";
            panelArsip.classList.remove('hidden');
            panelAktif.classList.add('hidden');
        }
    }
</script>
@endsection