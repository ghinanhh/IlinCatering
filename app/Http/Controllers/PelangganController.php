<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Cart; 
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;
use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;

class PelangganController extends Controller
{
    public function dashboard()
    {
        $user_id = Auth::id();
        $totalPesanan = Order::where('user_id', $user_id)->count();
        $pesananAktif = Order::where('user_id', $user_id)->whereNotIn('status', ['done', 'selesai', 'canceled'])->count();
        $totalReview = Review::where('user_id', $user_id)->count();

        $orders = Order::with('items.menu')->where('user_id', $user_id)->latest()->get();

        return view('dashboard.index', compact('totalPesanan', 'pesananAktif', 'totalReview', 'orders'));
    }

    public function menu(Request $request)
    {
        $search = $request->query('search');
        $categoryFilter = $request->query('category');

        $menus = Menu::where('is_active', true)
            ->where(function($mainQuery) use ($search, $categoryFilter) {
                if (!empty($search)) {
                    $mainQuery->where(function($sub) use ($search) {
                        $sub->where('title', 'like', "%{$search}%")
                            ->orWhere('category', 'like', "%{$search}%");
                    });
                }
                if (!empty($categoryFilter)) {
                    $mainQuery->where('category', $categoryFilter);
                }
            })
            ->latest()
            ->get();

        return view('dashboard.menu', compact('menus'));
    }

    public function addToCart(Request $request, $id)
    {
        $cart = Cart::where('user_id', Auth::id())->where('menu_id', $id)->first();
        $cart ? $cart->increment('quantity') : Cart::create(['user_id' => Auth::id(), 'menu_id' => $id, 'quantity' => 1]);
        return redirect()->back()->with('success', 'Menu berhasil ditambahkan!');
    }

    public function updateQuantity(Request $request, $id)
    {
        $cart = Cart::where('user_id', Auth::id())->where('id', $id)->first();
        
        if ($cart) {
            if ($request->action === 'increase') {
                $cart->increment('quantity');
            } elseif ($request->action === 'decrease') {
                $cart->quantity > 1 ? $cart->decrement('quantity') : $cart->delete();
            } elseif ($request->action === 'manual' || $request->has('qty') || $request->has('quantity')) {
                $qtyInput = $request->input('qty') ?? $request->input('quantity');
                $qty = (int) $qtyInput;
                $qty > 0 ? $cart->update(['quantity' => $qty]) : $cart->delete();
            }
        }
        
        return redirect()->back()->with('success', 'Jumlah pesanan berhasil diperbarui!');
    }

    public function removeItem($id)
    {
        Cart::where('user_id', Auth::id())->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Menu dihapus dari keranjang');
    }

    public function updateNote(Request $request, $id)
    {
        $cart = Cart::where('user_id', Auth::id())->where('id', $id)->first();
        if ($cart) $cart->update(['notes' => $request->notes]);
        return redirect()->back()->with('success', 'Catatan diperbarui!');
    }

    public function keranjang()
    {
        $cartItems = Cart::with('menu')->where('user_id', Auth::id())->get();
        
        $bookedDates = Order::whereNotIn('status', ['batal', 'canceled', 'expired'])
            ->where('user_id', '!=', Auth::id())
            ->pluck('event_date')
            ->map(fn($date) => date('Y-m-d', strtotime($date)))
            ->toArray();

        try {
            $admin = User::whereNotNull('google_calendar_token')
                         ->where('google_calendar_token', '!=', 'null')
                         ->first();

            if ($admin) {
                $token = json_decode($admin->google_calendar_token, true);
                if (is_array($token)) {
                    $client = new Client();
                    $client->setClientId(config('services.google.client_id'));
                    $client->setClientSecret(config('services.google.client_secret'));
                    $client->setAccessToken($token);

                    if ($client->isAccessTokenExpired() && $client->getRefreshToken()) {
                        $newToken = $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                        $admin->google_calendar_token = json_encode($newToken);
                        $admin->save();
                        $client->setAccessToken($newToken);
                    }

                    $service = new Calendar($client);
                    
                    $optParams = [
                        'timeMin' => date('c'),
                        'timeMax' => date('c', strtotime('+3 months')),
                        'singleEvents' => true,
                    ];
                    $events = $service->events->listEvents('primary', $optParams);

                    foreach ($events->getItems() as $event) {
                        $start = $event->getStart();
                        $dateStr = $start->getDateTime() ?? $start->getDate();
                        if ($dateStr) {
                            $formattedDate = date('Y-m-d', strtotime($dateStr));
                            if (!in_array($formattedDate, $bookedDates)) {
                                $bookedDates[] = $formattedDate;
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error('Gagal sinkronisasi Google Calendar ke Halaman Pelanggan: ' . $e->getMessage());
        }

        return view('dashboard.cart', compact('cartItems', 'bookedDates'));
    }

    public function processCheckout(Request $request)
    {
        $request->validate([
            'recipient_name' => 'required', 'phone_number' => 'required', 'address' => 'required',
            'event_date' => 'required', 'event_time' => 'required', 'cart_notes' => 'nullable|array',
            'payment_option' => 'required|in:dp,lunas'
        ]);

        $cartItems = Cart::where('user_id', Auth::id())->get();
        if ($cartItems->isEmpty()) return redirect()->back()->with('error', 'Keranjang kosong.');

        $totalPorsi = $cartItems->sum('quantity');
        if ($totalPorsi < 10) {
            return redirect()->back()->with('error', 'Mohon maaf, total pemesanan katering minimal wajib 10 porsi.');
        }

        $existingOrderCount = Order::where('event_date', $request->event_date)
            ->whereNotIn('status', ['batal', 'canceled', 'expired'])
            ->where('user_id', '!=', Auth::id())
            ->count();

        if ($existingOrderCount >= 1) {
            return redirect()->back()
                ->with('error', 'Maaf, kuota pemesanan untuk tanggal ' . date('d-m-Y', strtotime($request->event_date)) . ' sudah penuh. Silakan pilih tanggal alternatif lain!')
                ->withInput();
        }

        $totalPrice = $cartItems->sum(fn($i) => $i->menu->price * $i->quantity);

        if ($request->payment_option === 'lunas') {
            $dpAmount = $totalPrice;
            $remainingPayment = 0;
        } else {
            $dpAmount = $totalPrice * 0.3;
            $remainingPayment = $totalPrice * 0.7;
        }

        $order = Order::create([
            'user_id' => Auth::id(), 'order_number' => 'ILN-' . strtoupper(Str::random(8)),
            'recipient_name' => $request->recipient_name, 'phone_number' => $request->phone_number,
            'address' => $request->address, 'event_date' => $request->event_date,
            'event_time' => $request->event_time, 'total_price' => $totalPrice,
            'dp_amount' => $dpAmount, 'remaining_payment' => $remainingPayment,
            'status' => 'pending', 'payment_status' => 'pending',
        ]);

        foreach ($cartItems as $cart) {
            OrderItem::create([
                'order_id' => $order->id, 'menu_id' => $cart->menu_id,
                'quantity' => $cart->quantity, 'price' => $cart->menu->price,
                'notes' => $request->cart_notes[$cart->id] ?? $cart->notes
            ]);
        }

        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        try {
            $snapToken = Snap::getSnapToken(['transaction_details' => ['order_id' => $order->order_number, 'gross_amount' => (int) $order->dp_amount]]);
            $order->update(['snap_token' => $snapToken]);
        } catch (\Exception $e) { \Log::error($e->getMessage()); }

        Cart::where('user_id', Auth::id())->delete();
        return redirect()->route('pelanggan.checkout', $order->id)->with('success', 'Pesanan dibuat!');
    }

    public function checkout($id = null)
    {
        $user_id = Auth::id();
        $order = $id 
            ? Order::with('items.menu')->where('user_id', $user_id)->where('id', $id)->firstOrFail()
            : Order::with('items.menu')->where('user_id', $user_id)->latest()->firstOrFail();

        return view('dashboard.checkout', compact('order'));
    }

    public function storeReview(Request $request)
    {
        $request->validate([
            'order_id' => 'required', 'menu_id' => 'required', 'rating' => 'required', 
            'comment' => 'required', 'user_title' => 'nullable|string'
        ]);
        
        $imagePath = null;
        if ($request->hasFile('image')) {
            $filename = time() . '_' . Str::random(8) . '.jpg';
            $destinationPath = public_path('storage/reviews');
            if (!file_exists($destinationPath)) { mkdir($destinationPath, 0755, true); }

            $imageSource = imagecreatefromstring(file_get_contents($request->file('image')));
            imagejpeg($imageSource, $destinationPath . '/' . $filename, 60);
            imagedestroy($imageSource); 

            $imagePath = 'storage/reviews/' . $filename;
        }

        Review::create([
            'user_id' => Auth::id(), 'order_id' => $request->order_id, 'menu_id' => $request->menu_id,
            'rating' => $request->rating, 'comment' => $request->comment, 'image' => $imagePath,
            'user_title' => $request->user_title 
        ]);

        return redirect()->back()->with('success', 'Ulasan dikirim!');
    }

    public function handleNotification(Request $request)
    {
        $payload = $request->getContent();
        $notification = json_decode($payload);
        $order = Order::where('order_number', $notification->order_id)->first();
        
        if ($order) {
            $transaction = $notification->transaction_status;
            
            if (in_array($transaction, ['settlement', 'capture'])) {
                $status = ($order->remaining_payment == 0 ? 'lunas' : 'lunas dp');
                $order->update(['status' => $status, 'payment_status' => $transaction]);
                $this->addToGoogleCalendar($order);
            } 
            elseif (in_array($transaction, ['expire', 'cancel', 'deny'])) {
                $order->update(['status' => 'canceled', 'payment_status' => $transaction]);
            } 
            else {
                $order->update(['payment_status' => $transaction]);
            }
        }
        return response()->json(['message' => 'ok']);
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        if (in_array(strtolower($request->status), ['done', 'selesai'])) {
            $today = date('Y-m-d');
            if ($today < $order->event_date) {
                return redirect()->back()->with('error', 'Gagal! Pesanan tidak dapat diselesaikan sebelum tanggal hari H acara (' . date('d-m-Y', strtotime($order->event_date)) . ').');
            }
        }

        $order->update(['status' => $request->status]);

        if (in_array($request->status, ['confirmed', 'cooking', 'lunas dp', 'lunas', 'konfirmasi', 'dimasak'])) {
            $this->addToGoogleCalendar($order);
        }

        return redirect()->back()->with('success', 'Status pesanan diupdate ke ' . $request->status);
    }

    public function showKurirValidasi($order_number)
    {
        $order = Order::where('order_number', $order_number)->firstOrFail();
        
        if ($order->status === 'selesai') {
            return "
                <div style='text-align: center; font-family: sans-serif; padding: 50px;'>
                    <h1 style='color: #10b981;'>🍱 Ilin Catering</h1>
                    <p style='color: #64748b;'>Pesanan <strong>{$order_number}</strong> sudah selesai divalidasi sebelumnya.</p>
                </div>
            ";
        }

        return "
            <div style='text-align: center; font-family: sans-serif; padding: 40px 20px; background: #f8fafc; min-height: 100vh; display: flex; align-items: center; justify-content: center;'>
                <div style='background: white; max-width: 400px; width: 100%; padding: 30px; border-radius: 24px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;'>
                    <h2 style='color: #0f172a; margin-bottom: 5px; font-weight: 800;'>Konfirmasi Hantaran</h2>
                    <p style='color: #64748b; font-size: 13px; margin-bottom: 25px;'>Ilin Catering Lapangan</p>
                    <div style='background: #f1f5f9; padding: 15px; border-radius: 16px; text-align: left; margin-bottom: 25px;'>
                        <p style='margin: 0; font-size: 11px; color: #64748b; font-weight: bold; text-transform: uppercase;'>No. Pesanan</p>
                        <p style='margin: 3px 0 10px 0; font-size: 16px; font-weight: bold; color: #0f172a;'>#{$order_number}</p>
                        <p style='margin: 0; font-size: 11px; color: #64748b; font-weight: bold; text-transform: uppercase;'>Penerima</p>
                        <p style='margin: 3px 0 0 0; font-size: 14px; font-weight: bold; color: #334155;'>{$order->recipient_name}</p>
                    </div>
                    <form action='".route('kurir.validasi.submit', $order_number)."' method='POST'>
                        <input type='hidden' name='_token' value='".csrf_token()."'>
                        <button type='submit' style='background: #16a34a; color: white; border: none; width: 100%; padding: 15px; border-radius: 16px; font-weight: bold; font-size: 14px; cursor: pointer; box-shadow: 0 4px 12px rgba(22,163,74,0.2); outline: none;'>
                            ✅ YA, PESANAN SAMPAI & COD LUNAS
                        </button>
                    </form>
                </div>
            </div>
        ";
    }

    public function kurirValidasiCod($order_number)
    {
        $order = Order::where('order_number', $order_number)->firstOrFail();
        
        $order->update([
            'status' => 'selesai',
            'payment_status' => 'settlement',
            'remaining_payment' => 0
        ]);

        return "
            <div style='text-align: center; font-family: sans-serif; padding: 50px; background: #f8fafc; min-height: 100vh;'>
                <div style='background: white; max-width: 400px; margin: 0 auto; padding: 30px; border-radius: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);'>
                    <div style='font-size: 50px;'>✅</div>
                    <h2 style='color: #0f172a; margin-top: 10px;'>Hantaran Selesai!</h2>
                    <p style='color: #475569; font-size: 14px;'>Nomor Pesanan: <strong>{$order_number}</strong></p>
                    <hr style='border: 0; border-top: 1px dashed #cbd5e1; margin: 20px 0;'>
                    <p style='color: #16a34a; font-weight: bold; font-size: 15px;'>Pembayaran COD Telah Lunas</p>
                    <p style='color: #64748b; font-size: 12px; margin-top: 5px;'>Data telah disinkronkan ke Google Calendar & Dashboard Admin secara real-time.</p>
                </div>
                <p style='font-size: 11px; color: #94a3b8; margin-top: 30px;'>&copy; 2026 Ilin Catering. Made with ♡ by Ghina.</p>
            </div>
        ";
    }

    private function addToGoogleCalendar($order)
    {
        try {
            $admin = User::whereNotNull('google_calendar_token')
                         ->where('google_calendar_token', '!=', 'null')
                         ->first();
            
            if (!$admin) return;

            $token = json_decode($admin->google_calendar_token, true);
            if (!is_array($token)) return;

            $client = new Client();
            $client->setClientId(config('services.google.client_id'));
            $client->setClientSecret(config('services.google.client_secret'));
            $client->setAccessToken($token);

            if ($client->isAccessTokenExpired() && $client->getRefreshToken()) {
                $newToken = $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                $admin->google_calendar_token = json_encode($newToken);
                $admin->save();
                $client->setAccessToken($newToken);
            }

            $service = new Calendar($client);

            $orderNumber = $order->order_number ?? $order->id;
            $optParams = ['q' => 'Pesanan #' . $orderNumber, 'maxResults' => 1];
            $existingEvents = $service->events->listEvents('primary', $optParams);
            if (count($existingEvents->getItems()) > 0) { return; }

            $orderWithItems = Order::with('items.menu')->find($order->id);
            $menuList = "";
            foreach ($orderWithItems->items as $item) {
                $menuList .= "- " . $item->menu->title . " (" . $item->quantity . "x)\n";
            }

            $startDateTime = $order->event_date . 'T' . $order->event_time; 
            $endDateTime = date('Y-m-d\TH:i:s', strtotime($startDateTime . ' +2 hours'));
            $buyerName = $order->recipient_name ?? ($order->user->name ?? 'Pelanggan Offline');

            $event = new Event([
                'summary' => '🍳 Jadwal Masak: Pesanan #' . $orderNumber . ' - ' . $buyerName,
                'location' => $order->address ?? 'Alamat Ilin Catering',
                'description' => "Daftar Masakan:\n" . $menuList . "\nKontak: " . ($order->phone_number ?? '-'),
                'start' => ['dateTime' => $startDateTime, 'timeZone' => 'Asia/Makassar'],
                'end' => ['dateTime' => $endDateTime, 'timeZone' => 'Asia/Makassar'],
                'reminders' => [
                    'useDefault' => false,
                    'overrides' => [
                        ['method' => 'popup', 'minutes' => 4320], 
                        ['method' => 'email', 'minutes' => 4320], 
                        ['method' => 'popup', 'minutes' => 1440], 
                    ],
                ],
            ]);

            $service->events->insert('primary', $event);
        } catch (\Exception $e) {
            \Log::error('Gagal sinkronisasi Google Calendar: ' . $e->getMessage());
        }
    }

    public function adminReviewIndex()
    {
        $reviews = Review::with(['user', 'menu'])->latest()->get();
        return view('dashboard.admin.review', compact('reviews'));
    }

    public function updateReviewReply(Request $request, $id)
    {
        $request->validate(['admin_reply' => 'required']);
        Review::findOrFail($id)->update(['admin_reply' => $request->admin_reply]);
        return redirect()->back()->with('success', 'Balasan dikirim!');
    }

    // 🌟 FIX: Kurung siku nakal di line 447 sudah diubah menjadi tanda kurung lingkaran biasa )
    public function cetakNota($id)
    {
        $order = Order::with('items.menu')->where('user_id', Auth::id())->where('id', $id)->firstOrFail();
        return view('dashboard.nota', compact('order'));
    }

    public function riwayat()
    {
        $user_id = Auth::id();
        $totalPesanan = Order::where('user_id', $user_id)->count();
        $pesananAktif = Order::where('user_id', $user_id)->whereNotIn('status', ['done', 'selesai', 'canceled'])->count();
        $totalReview = Review::where('user_id', $user_id)->count();

        $orders = Order::with('items.menu')
            ->where('user_id', $user_id)
            ->whereIn('status', ['done', 'selesai', 'DONE', 'Selesai'])
            ->latest()
            ->get();

        return view('dashboard.riwayat', compact('totalPesanan', 'pesananAktif', 'totalReview', 'orders'));
    }
}