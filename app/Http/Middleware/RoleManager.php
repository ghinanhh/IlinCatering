<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
// --- TAMBAHKAN BARIS INI ---
use Illuminate\Support\Facades\Auth; 
// ---------------------------

class RoleManager
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        // Cek apakah user sudah login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Cek apakah role user sesuai dengan yang diminta (admin/owner/pelanggan)
        if ($request->user()->role !== $role) {
            // Kita arahkan ke dashboard saja kalau dia salah masuk, biar gak logout paksa
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}