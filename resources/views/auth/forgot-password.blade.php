<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password | Ilin Catering</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-slate-50 font-sans text-slate-900 flex items-center justify-center min-h-screen p-4">

    <div class="max-w-md w-full bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 p-8 sm:p-10 border border-slate-100 relative overflow-hidden">
        
        <div class="absolute -top-20 -right-20 w-40 h-40 bg-orange-100 rounded-full blur-3xl opacity-50"></div>
        <div class="absolute -bottom-20 -left-20 w-40 h-40 bg-orange-100 rounded-full blur-3xl opacity-50"></div>

        <div class="relative z-10">
            <a href="{{ route('login') }}" class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-slate-50 text-slate-400 hover:text-orange-600 hover:bg-orange-50 transition mb-6">
                <i class="fa-solid fa-arrow-left"></i>
            </a>

            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-orange-100 text-orange-600 rounded-2xl flex items-center justify-center text-2xl mx-auto mb-4 shadow-inner">
                    <i class="fa-solid fa-key"></i>
                </div>
                <h1 class="text-2xl font-extrabold text-slate-900">Lupa Password?</h1>
                <p class="text-slate-500 mt-2 text-sm leading-relaxed">Jangan panik! Masukkan email yang terdaftar, dan kami akan mengirimkan tautan untuk mereset password Anda.</p>
            </div>

            @if (session('status'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-2xl mb-6 text-sm flex items-start gap-3">
                    <i class="fa-solid fa-circle-check mt-0.5"></i>
                    <p>{{ session('status') }}</p>
                </div>
            @endif

            <form action="{{ route('password.email') }}" method="POST" class="space-y-6">
                @csrf
                
                <div>
                    <label for="email" class="block text-sm font-bold text-slate-700 mb-2 pl-1">Email Terdaftar</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fa-regular fa-envelope text-slate-400"></i>
                        </div>
                        <input type="email" name="email" id="email" required autofocus
                            class="block w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm focus:ring-orange-500 focus:border-orange-500 transition shadow-sm @error('email') border-red-500 @enderror" 
                            placeholder="nama@email.com" value="{{ old('email') }}">
                    </div>
                    @error('email')
                        <p class="text-red-500 text-xs font-bold mt-2 pl-1 flex items-center gap-1">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <button type="submit" class="w-full bg-orange-600 text-white font-bold py-3.5 px-4 rounded-2xl hover:bg-orange-700 focus:ring-4 focus:ring-orange-200 transition flex items-center justify-center gap-2 shadow-lg shadow-orange-200">
                    <i class="fa-regular fa-paper-plane"></i> Kirim Link Reset Password
                </button>
            </form>
        </div>
    </div>

</body>
</html>