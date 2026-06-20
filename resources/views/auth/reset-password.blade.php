<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Password Baru | Ilin Catering</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-slate-50 font-sans text-slate-900 flex items-center justify-center min-h-screen p-4">

    <div class="max-w-md w-full bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 p-8 sm:p-10 border border-slate-100 relative overflow-hidden">
        
        <div class="absolute -top-20 -right-20 w-40 h-40 bg-orange-100 rounded-full blur-3xl opacity-50"></div>
        <div class="absolute -bottom-20 -left-20 w-40 h-40 bg-orange-100 rounded-full blur-3xl opacity-50"></div>

        <div class="relative z-10">
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-orange-100 text-orange-600 rounded-2xl flex items-center justify-center text-2xl mx-auto mb-4 shadow-inner">
                    <i class="fa-solid fa-lock"></i>
                </div>
                <h1 class="text-2xl font-extrabold text-slate-900">Buat Password Baru</h1>
                <p class="text-slate-500 mt-2 text-sm leading-relaxed">Silakan masukkan password baru Anda yang kuat dan mudah diingat.</p>
            </div>

            <form action="{{ route('password.update') }}" method="POST" class="space-y-5">
                @csrf
                
                <input type="hidden" name="token" value="{{ $token }}">

                <div>
                    <label for="email" class="block text-sm font-bold text-slate-700 mb-2 pl-1">Email Anda</label>
                    <input type="email" name="email" id="email" value="{{ $email ?? old('email') }}" readonly
                        class="block w-full px-4 py-3.5 bg-slate-100 border border-slate-200 rounded-2xl text-sm text-slate-500 cursor-not-allowed">
                    @error('email')
                        <p class="text-red-500 text-xs font-bold mt-2 pl-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-bold text-slate-700 mb-2 pl-1">Password Baru</label>
                    <input type="password" name="password" id="password" required autofocus
                        class="block w-full px-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm focus:ring-orange-500 focus:border-orange-500 transition shadow-sm" 
                        placeholder="Minimal 8 karakter">
                    @error('password')
                        <p class="text-red-500 text-xs font-bold mt-2 pl-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-bold text-slate-700 mb-2 pl-1">Ulangi Password Baru</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                        class="block w-full px-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm focus:ring-orange-500 focus:border-orange-500 transition shadow-sm" 
                        placeholder="Ketik ulang password baru">
                </div>

                <button type="submit" class="w-full bg-orange-600 text-white font-bold py-3.5 px-4 rounded-2xl hover:bg-orange-700 focus:ring-4 focus:ring-orange-200 transition flex items-center justify-center gap-2 shadow-lg shadow-orange-200 mt-4">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Password Baru
                </button>
            </form>
        </div>
    </div>

</body>
</html>