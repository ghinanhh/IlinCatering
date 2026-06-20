<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Ilin Catering</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-slate-50 font-sans min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full bg-white rounded-[2.5rem] shadow-2xl shadow-orange-100 overflow-hidden border border-slate-100">
        <div class="p-10">
            <div class="text-center mb-10">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-orange-100 text-orange-600 rounded-2xl text-3xl mb-4">
                    <i class="fa-solid fa-utensils"></i>
                </div>
                <h2 class="text-3xl font-extrabold text-slate-900">Selamat Datang!</h2>
                <p class="text-slate-500 mt-2 text-sm">Masuk untuk mulai memesan hidangan lezat.</p>
            </div>

            <form action="{{ route('login') }}" method="POST" class="space-y-6">
                @csrf 

                @if($errors->any())
                <div class="bg-red-50 text-red-600 p-4 rounded-2xl text-sm border border-red-100">
                    <i class="fa-solid fa-circle-exclamation mr-2"></i> {{ $errors->first() }}
                </div>
                @endif

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                            <i class="fa-regular fa-envelope"></i>
                        </span>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="contoh@mail.com" class="w-full pl-11 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition" required>
                    </div>
                </div>

                <div x-data="{ show: false }">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        
                        <input :type="show ? 'text' : 'password'" name="password" placeholder="••••••••" class="w-full pl-11 pr-12 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition" required>
                        
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-orange-600 transition">
                            <i class="fa-solid" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center text-slate-600 pointer-cursor">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-orange-600 focus:ring-orange-500 mr-2"> 
                        Ingat Saya
                    </label>
                    
                    <a href="{{ route('password.request') }}" class="text-orange-600 font-bold hover:underline">
                        Lupa Password?
                    </a>
                </div>

                <button type="submit" class="w-full py-4 bg-orange-600 text-white rounded-2xl font-bold shadow-lg shadow-orange-200 hover:bg-orange-700 transition transform hover:-translate-y-1">
                    Masuk Sekarang
                </button>
            </form>

            <div class="mt-6 flex items-center justify-between">
                <span class="border-b w-1/5 lg:w-1/4 border-slate-200"></span>
                <span class="text-xs text-center text-slate-500 font-semibold uppercase">Atau masuk dengan</span>
                <span class="border-b w-1/5 lg:w-1/4 border-slate-200"></span>
            </div>

            <div class="mt-6">
                <a href="{{ route('google.login') }}" class="w-full flex items-center justify-center py-3 px-4 bg-white border border-slate-300 text-slate-700 rounded-2xl font-bold shadow-sm hover:bg-slate-50 transition transform hover:-translate-y-1">
                    <svg class="h-5 w-5 mr-3" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                    </svg>
                    Google
                </a>
            </div>
            <div class="mt-10 text-center text-sm text-slate-500">
                Belum punya akun? 
                <a href="{{ route('register') }}" class="text-orange-600 font-bold hover:underline">Daftar Gratis</a>
            </div>
        </div>
    </div>

</body>
</html>