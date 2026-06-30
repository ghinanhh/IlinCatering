<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Cart; 
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Review;
use App\Models\User; // 🌟 TAMBAHAN: Import Model User
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;
use Google\Client; // 🌟 TAMBAHAN: Import Google Client
use Google\Service\Calendar; // 🌟 TAMBAHAN: Import Google Calendar
use Google\Service\Calendar\Event; // 🌟 TAMBAHAN: Import Google Event

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
                // 🌟 FIX UPDATE QUANTITY: Menerima input ketikan manual (Enter/Blur) dari request 'qty'
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
        
        // 🌟 REVISI DOSEN: Tanggal penuh tidak berlaku diblokir jika yang memesan adalah user yang sama
        $bookedDates = Order::whereNotIn('status', ['batal', 'canceled', 'expired'])
            ->where('user_id', '!=', Auth::id())
            ->pluck('event_date')
            ->toArray();

        return view('dashboard.cart', compact('cartItems', 'bookedDates'));
    }

    public function processCheckout(Request $request)
    {
        $request->validate([
            'recipient_name' => 'required', 'phone_number' => 'required', 'address' => 'required',
            'event_date' => 'required', 'event_time' => 'required', 'cart_notes' => 'nullable|array'
        ]);

        $cartItems = Cart::where('user_id', Auth::id())->get();
        if ($cartItems->isEmpty()) return redirect()->back()->with('error', 'Keranjang kosong.');

        // 🌟 AMANKAN BACK-END: Validasi total porsi wajib minimal 10 porsi sebelum simpan ke database
        $totalPorsi = $cartItems->sum('quantity');
        if ($totalPorsi < 10) {
            return redirect()->back()->with('error', 'Mohon maaf, total pemesanan katering minimal wajib 10 porsi.');
        }

        // 🌟 REVISI DOSEN: Hitung kuota tanggal penuh hanya dari pesanan ORANG LAIN (User sama bisa tambah pesanan)
        $existingOrderCount = Order::where('event_date', $request->event_date)
            ->whereNotIn('status', ['batal', 'canceled', 'expired'])
            ->where('user_id', '!=', Auth::id())
            ->count();

        if ($existingOrderCount >= 1) {
            return redirect()->back()
                ->with('error', 'Maaf, kuota pemesanan untuk tanggal ' . date('d-m-Y', strtotime($request->event_date)) . ' sudah penuh (Ilin Catering menerapkan batas maksimal 1 pesanan besar per hari). Silakan pilih tanggal alternatif lain!')
                ->withInput();
        }

        $totalPrice = $cartItems->sum(fn($i) => $i->menu->price * $i->quantity);
        $order = Order::create([
            'user_id' => Auth::id(), 'order_number' => 'ILN-' . strtoupper(Str::random(8)),
            'recipient_name' => $request->recipient_name, 'phone_number' => $request->phone_number,
            'address' => $request->address, 'event_date' => $request->event_date,
            'event_time' => $request->event_time, 'total_price' => $totalPrice,
            'dp_amount' => $totalPrice * 0.3, 'remaining_payment' => $totalPrice * 0.7,
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
            'order_id' => 'required', 
            'menu_id' => 'required', 
            'rating' => 'required', 
            'comment' => 'required',
            'user_title' => 'nullable|string'
        ]);
        
        $imagePath = null;
        
        if ($request->hasFile('image')) {
            $filename = time() . '_' . Str::random(8) . '.jpg';
            
            $destinationPath = public_path('storage/reviews');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $imageSource = imagecreatefromstring(file_get_contents($request->file('image')));
            imagejpeg($imageSource, $destinationPath . '/' . $filename, 60);
            imagedestroy($imageSource); 

            $imagePath = 'storage/reviews/' . $filename;
        }

        Review::create([
            'user_id' => Auth::id(), 
            'order_id' => $request->order_id, 
            'menu_id' => $request->menu_id,
            'rating' => $request->rating, 
            'comment' => $request->comment, 
            'image' => $imagePath,
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
            $status = in_array($notification->transaction_status, ['settlement', 'capture']) ? 'lunas dp' : 'pending';
            $order->update(['status' => $status, 'payment_status' => $notification->transaction_status]);

            // 🚀 SAMBUNGAN KABEL: Jika berhasil lunas dp lewat Midtrans, kirim otomatis ke Google Calendar
            if ($status === 'lunas dp') {
                $this->addToGoogleCalendar($order);
            }
        }
        return response()->json(['message' => 'ok']);
    }

    // FUNGSI BARU: Update Status via Admin di PelangganController
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => $request->status]);

        // 🚀 SAMBUNGAN KABEL: Kirim ke Google Calendar jika status valid
        if (in_array($request->status, ['confirmed', 'cooking', 'lunas dp', 'konfirmasi', 'dimasak'])) {
            $this->addToGoogleCalendar($order);
        }

        return redirect()->back()->with('success', 'Status pesanan diupdate ke ' . $request->status);
    }

    /**
     * 🍳 KODINGAN AMAN GOOGLE CALENDAR UNTUK WEBHOOK MIDTRANS + ANTI DUPLIKAT
     */
    private function addToGoogleCalendar($order)
    {
        try {
            // Failsafe: Cari akun user manapun yang menyimpan token Google Calendar di database
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

            if ($client->isAccessTokenExpired()) {
                if ($client->getRefreshToken()) {
                    $newToken = $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                    $admin->google_calendar_token = json_encode($newToken);
                    $admin->save();
                    $client->setAccessToken($newToken);
                } else {
                    return;
                }
            }

            $service = new Calendar($client);

            // 🌟 LOGIKA ANTI-DUPLIKAT: Cek apakah event dengan nomor order ini sudah ada di Google Calendar
            $orderNumber = $order->order_number ?? $order->id;
            $optParams = [
                'q' => 'Pesanan #' . $orderNumber,
                'maxResults' => 1,
            ];
            $existingEvents = $service->events->listEvents('primary', $optParams);
            
            // Jika sudah ada record event yang sama, batalkan pembuatan agar tidak double!
            if (count($existingEvents->getItems()) > 0) {
                return; 
            }

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
                'description' => "Daftar Masakan yang Harus Disiapkan:\n" . $menuList . "\nCatatan Kontak: " . ($order->phone_number ?? '-'),
                'start' => [
                    'dateTime' => $startDateTime,
                    'timeZone' => 'Asia/Makassar',
                ],
                'end' => [
                    'dateTime' => $endDateTime,
                    'timeZone' => 'Asia/Makassar',
                ],
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

    public function cetakNota($id)
    {
        $order = Order::with('items.menu')
                      ->where('user_id', Auth::id())
                      ->where('id', $id)
                      ->firstOrFail();

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