<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Client;
use Google\Service\Calendar;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GoogleCalendarController extends Controller
{
    // Fungsi untuk membuat objek Google Client
    private function getGoogleClient()
    {
        $client = new Client();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri(config('services.google.calendar_redirect'));
        
        // Meminta izin akses khusus kalender sesuai scope yang kita centang tadi
        $client->addScope(Calendar::CALENDAR_EVENTS);
        
        // PENTING: Supaya Google memberikan 'refresh_token' agar login tidak gampang kedaluwarsa
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');

        return $client;
    }

    // 1. Fungsi mengarahkan Admin ke halaman login Google
    public function redirectToGoogle()
    {
        $client = $this->getGoogleClient();
        $authUrl = $client->createAuthUrl();
        
        return redirect()->away($authUrl);
    }

    // 2. Fungsi menerima balasan dari Google dan menyimpan token ke database
    public function handleGoogleCallback(Request $request)
    {
        if (!$request->has('code')) {
            return redirect()->route('dashboard')->with('error', 'Akses Google Calendar dibatalkan.');
        }

        $client = $this->getGoogleClient();
        
        // Tukarkan kode dari Google dengan Token Resmi
        $token = $client->fetchAccessTokenWithAuthCode($request->get('code'));

        // Cek apakah ada error saat penukaran token
        if (isset($token['error'])) {
            return redirect()->route('dashboard')->with('error', 'Gagal mengambil token akses Google.');
        }

        // Simpan token dalam bentuk JSON ke dalam database user admin yang sedang login
        $user = Auth::user();
        $user->google_calendar_token = json_encode($token);
        $user->save();

        return redirect()->route('dashboard')->with('success', 'Google Calendar berhasil terhubung secara otomatis!');
    }
}