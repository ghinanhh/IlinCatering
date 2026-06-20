@extends('layouts.app')

@section('content')
<div class="p-6 bg-slate-50 min-h-screen">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-slate-900">Manajemen Review ⭐</h1>
        <p class="text-slate-500">Daftar ulasan pelanggan untuk menjaga kualitas konten web Ilin Catering.</p>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-500 text-white rounded-2xl shadow-lg flex items-center gap-3">
            <i class="fa-solid fa-check-circle"></i>
            <span class="text-xs font-black uppercase">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400">Pelanggan</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400">Menu & Rating</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 w-1/3">Komentar</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400">Bukti Foto</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 text-center">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($reviews as $review)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="px-6 py-4 align-top">
                            <p class="font-bold text-slate-900 text-sm">{{ $review->user->name }}</p>
                            <p class="text-[9px] text-orange-500 uppercase font-black tracking-widest mt-0.5">
                                {{ $review->user_title ?? 'Pelanggan Umum' }}
                            </p>
                        </td>
                        <td class="px-6 py-4 align-top">
                            <p class="text-xs font-bold text-slate-700">{{ $review->menu->title ?? 'Menu Dihapus' }}</p>
                            <div class="flex text-yellow-400 text-[10px] mt-1">
                                {{-- 🌟 PERBAIKAN: Perulangan bintang sudah menggunakan <= 5 dengan aman --}}
                                @for($i=1; $i<=5; $i++)
                                    <i class="fa-solid fa-star {{ $i <= $review->rating ? '' : 'text-slate-200' }}"></i>
                                @endfor
                            </div>
                        </td>
                        <td class="px-6 py-4 align-top">
                            <p class="text-xs text-slate-600 italic leading-relaxed">"{{ $review->comment }}"</p>
                            
                            <div class="mt-3 pt-3 border-t border-slate-100">
                                @if($review->admin_reply)
                                    <div class="bg-blue-50/70 border border-blue-100 p-3 rounded-xl flex items-start gap-2.5">
                                        <span class="text-xs">📢</span>
                                        <div>
                                            <p class="text-[9px] font-black text-blue-900 uppercase tracking-wider">Balasan Admin:</p>
                                            <p class="text-xs text-blue-800 mt-0.5 font-medium">"{{ $review->admin_reply }}"</p>
                                        </div>
                                    </div>
                                @else
                                    <form action="{{ route('admin.reviews.reply', $review->id) }}" method="POST" class="mt-1">
                                        @csrf
                                        <div class="flex gap-2">
                                            <input type="text" name="admin_reply" class="flex-1 px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:ring-1 focus:ring-orange-500 focus:bg-white transition" placeholder="Tulis komentar balasan admin..." required>
                                            <button type="submit" class="bg-slate-950 text-white px-3 py-1.5 rounded-xl text-[10px] font-bold hover:bg-orange-600 transition tracking-wide shrink-0 shadow-sm">
                                                Balas
                                            </button>
                                        </div>
                                    </form>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 align-top">
                            @if($review->image)
                                <a href="{{ asset($review->image) }}" target="_blank">
                                    <img src="{{ asset($review->image) }}" class="w-12 h-12 rounded-xl object-cover border border-slate-200 hover:scale-110 transition">
                                </a>
                            @else
                                <span class="text-[8px] text-slate-300 font-bold uppercase tracking-tighter italic">Tanpa Foto</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center align-top">
                            <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" onsubmit="return confirm('Hapus ulasan ini secara permanen?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-3 bg-red-50 text-red-500 rounded-xl hover:bg-red-500 hover:text-white transition shadow-sm">
                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-20 text-center text-slate-400 italic text-sm">
                            <i class="fa-solid fa-inbox block text-3xl mb-2 opacity-20"></i>
                            Belum ada ulasan dari pelanggan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection