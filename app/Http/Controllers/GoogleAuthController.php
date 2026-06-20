<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Exception;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        // Mengarahkan user ke halaman login Google
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            // Mengambil data user dari Google
            $googleUser = Socialite::driver('google')->user();

            // Cek apakah user ini sudah pernah login pakai Google sebelumnya
            $user = User::where('google_id', $googleUser->id)->first();

            if (!$user) {
                // Jika tidak ketemu, cek apakah emailnya sudah terdaftar manual
                $user = User::where('email', $googleUser->email)->first();

                if ($user) {
                    // Kalau email sudah ada, update/tambahkan google_id nya
                    $user->update(['google_id' => $googleUser->id]);
                } else {
                    // Kalau benar-benar user baru, buatkan akun pelanggan baru
                    $user = User::create([
                        'name' => $googleUser->name,
                        'email' => $googleUser->email,
                        'google_id' => $googleUser->id,
                        'role' => 'pelanggan', // Otomatis kita jadikan pelanggan
                        'password' => null, // Password kosong karena pakai Google
                    ]);
                }
            }

            // Daftarkan session login user tersebut
            Auth::login($user);

            // Arahkan ke halaman utama/dashboard setelah berhasil login
            return redirect()->intended('/dashboard'); // Sesuaikan '/dashboard' dengan rute setelah login web kamu

        } catch (Exception $e) {
            // Jika error, kembalikan ke halaman login dengan pesan
            return redirect('/login')->with('error', 'Terjadi kesalahan saat login dengan Google.');
        }
    }
}