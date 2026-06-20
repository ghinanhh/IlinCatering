@extends('layouts.app')

@section('content')
<div class="mb-6 sm:mb-8 text-center sm:text-left flex flex-col sm:flex-row justify-between items-center gap-4">
    <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Semua Review Pelanggan ⭐</h1>
        <p class="text-sm sm:text-base text-slate-500 mt-1 sm:mt-0">Kumpulan testimoni jujur dari penikmat Ilin Catering.</p>
    </div>
    <div class="px-4 py-2 bg-amber-50 border border-amber-200 text-amber-800 text-xs font-black uppercase tracking-wider rounded-xl shadow-sm">
        👑 Evaluasi Kualitas Rasa
    </div>
</div>

<div class="bg-white rounded-3xl sm:rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
    <div class="p-4 sm:p-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
            @forelse($reviews as $review)
            {{-- Kartu Ulasan Modern dengan Efek Hover & Dekorasi --}}
            <div class="p-5 sm:p-6 bg-white border border-slate-100 rounded-3xl shadow-sm hover:shadow-md hover:-translate-y-1 transition duration-300 relative flex flex-col justify-between overflow-hidden group">
                
                {{-- Dekorasi Tanda Kutip Transparan Pemotong Kepolosan Halaman --}}
                <i class="fa-solid fa-quote-right absolute right-4 top-4 text-4xl sm:text-5xl text-slate-100/70 pointer-events-none select-none group-hover:text-orange-100/50 transition duration-300"></i>

                <div>
                    {{-- Info Profil Pelanggan --}}
                    <div class="flex items-center gap-3 mb-4 relative z-10">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-orange-500 to-orange-600 text-white rounded-xl sm:rounded-2xl flex items-center justify-center font-black text-sm sm:text-base shadow-sm">
                            {{ strtoupper(substr($review->user->name, 0, 1)) }}
                        </div>
                        <div>
                            <h4 class="font-black text-slate-900 text-xs sm:text-sm tracking-tight">{{ $review->user->name }}</h4>
                            <p class="text-[9px] sm:text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-0.5">
                                <i class="fa-regular fa-calendar-minus mr-1"></i>{{ $review->created_at->format('d M Y') }}
                            </p>
                        </div>
                    </div>
                    
                    {{-- Kotak Inti Komentar Pelanggan --}}
                    <div class="space-y-2.5">
                        {{-- Badge Kapsul Menu Masakan --}}
                        <div class="inline-block bg-orange-50 border border-orange-100 text-orange-700 px-2.5 py-1 rounded-lg text-[9px] sm:text-[10px] font-black uppercase tracking-wide">
                            <i class="fa-solid fa-bowl-food mr-1"></i> Menu: {{ $review->menu->title }}
                        </div>
                        
                        {{-- Blok Rating Bintang Emas --}}
                        <div class="flex text-yellow-400 text-[10px] sm:text-xs gap-0.5">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fa-solid fa-star {{ $i <= $review->rating ? 'text-yellow-400 drop-shadow-sm' : 'text-slate-200' }}"></i>
                            @endfor
                        </div>
                        
                        {{-- Isi Teks Komentar --}}
                        <p class="text-xs text-slate-600 italic leading-relaxed bg-slate-50/50 p-3 rounded-xl border border-slate-100/50">
                            "{{ $review->comment }}"
                        </p>
                    </div>
                </div>

                {{-- Kotak Balasan Admin Respon --}}
                @if($review->admin_reply)
                <div class="mt-4 border-l-4 border-orange-500 bg-slate-50/80 p-3 rounded-r-xl transition duration-300 group-hover:bg-orange-50/20">
                    <p class="text-[9px] sm:text-[10px] font-black text-slate-500 uppercase mb-1 flex items-center gap-1.5">
                        <i class="fa-solid fa-reply text-orange-500 rotate-180"></i> Respon Admin Ilin
                    </p>
                    <p class="text-xs text-slate-700 font-medium leading-relaxed">
                        "{{ $review->admin_reply }}"
                    </p>
                </div>
                @endif
                
            </div>
            @empty
            {{-- Keadaan Kosong Yang Menarik --}}
            <div class="col-span-1 md:col-span-2 text-center py-16 sm:py-20 border border-dashed border-slate-200 rounded-[2rem] m-2">
                <div class="inline-flex items-center justify-center w-14 h-14 sm:w-16 sm:h-16 bg-slate-50 text-slate-300 rounded-full mb-4 shadow-inner">
                    <i class="fa-solid fa-comment-slash text-xl sm:text-2xl"></i>
                </div>
                <h4 class="text-slate-700 font-bold text-sm sm:text-base">Belum Ada Testimoni</h4>
                <p class="text-slate-400 italic text-xs mt-1 px-4">Kotak ulasan masih kosong. Ulasan pelanggan online akan otomatis terbit di halaman ini.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection