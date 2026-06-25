<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Menu; 
use App\Models\OrderItem; 
use Illuminate\Http\Request;
use Illuminate\Support\Str; 
use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user', 'items.menu'])
            ->whereNotIn('status', ['selesai', 'done', 'Selesai', 'DONE']) 
            ->latest()
            ->get();
            
        $menus = Menu::orderBy('title', 'asc')->get();
            
        return view('dashboard.admin.orders', compact('orders', 'menus'));
    }

    public function storeManualOrder(Request $request)
    {
        $request->validate([
            'recipient_name'       => 'required|string|min:3|max:255|regex:/^[a-zA-Z]/',
            'phone_number'         => 'required|numeric|min_digits:10',
            'address'              => 'required|string',
            'event_date'           => 'required|date|after_or_equal:today',
            'event_time'           => 'required',
            'menu_ids'             => 'required|array|min:1',
            'quantities'           => 'required|array|min:1',
            'notes'                => 'nullable|array',
            'payment_status_input' => 'required|in:belum_bayar,sudah_dp', 
        ], [
            'recipient_name.min'   => 'Nama pelanggan offline terlalu pendek! Minimal 3 karakter.',
            'recipient_name.regex' => 'Nama wajib diawali dengan huruf.',
            'phone_number.numeric' => 'Nomor HP wajib berupa angka murni.',
            'phone_number.min_digits' => 'Nomor HP minimal berjumlah 10 digit.',
            'menu_ids.required'    => 'Minimal pilih 1 menu masakan katering!',
        ]);

        $totalPrice = 0;
        $itemsData = [];

        foreach ($request->menu_ids as $index => $menuId) {
            $menu = Menu::findOrFail($menuId);
            $qty = intval($request->quantities[$index] ?? 1);
            if ($qty < 1) $qty = 1; 

            $subtotal = $menu->price * $qty;
            $totalPrice += $subtotal;

            $itemsData[] = [
                'menu_id'  => $menu->id,
                'quantity' => $qty,
                'price'    => $menu->price,
                'notes'    => $request->notes[$index] ?? null
            ];
        }

        $dpAmount = $totalPrice * 0.3;
        $remainingPayment = $totalPrice * 0.7;

        if ($request->payment_status_input === 'sudah_dp') {
            $dbOrderStatus   = 'lunas dp';
            $dbPaymentStatus = 'settlement';
            $successMessage  = 'Pesanan manual (Offline) BERHASIL dicatat dengan DP Lunas! Jadwal otomatis dikirim ke Google Calendar Dapur.';
        } else {
            $dbOrderStatus   = 'pending';
            $dbPaymentStatus = 'pending';
            $successMessage  = 'Pesanan manual (Offline) BERHASIL disimpan dengan status UTANG/PENDING DP! Dapur ditahan sampai dana diserahkan.';
        }

        $order = Order::create([
            'order_number'      => 'OFFLINE-' . strtoupper(Str::random(6)), 
            'user_id'           => auth()->id(), 
            'recipient_name'    => $request->recipient_name,
            'phone_number'      => $request->phone_number,
            'address'           => $request->address,
            'event_date'        => $request->event_date,
            'event_time'        => $request->event_time,
            'total_price'       => $totalPrice,
            'status'            => $dbOrderStatus,       
            'dp_amount'         => $dpAmount,
            'remaining_payment' => $remainingPayment,
            'snap_token'        => null,                 
            'payment_status'    => $dbPaymentStatus,     
        ]);

        foreach ($itemsData as $item) {
            $order->items()->create($item);
        }

        if ($dbOrderStatus === 'lunas dp') {
            $this->addToGoogleCalendar($order);
        }

        return redirect()->back()->with('success', $successMessage);
    }

    public function archive()
    {
        $archivedOrders = Order::with(['user', 'items.menu'])
            ->whereIn('status', ['selesai', 'done', 'Selesai', 'DONE']) 
            ->orderBy('event_date', 'desc') 
            ->orderBy('event_time', 'desc') 
            ->get();
            
        return view('dashboard.admin.archive', compact('archivedOrders'));
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => $request->status]);

        if (in_array($request->status, ['confirmed', 'cooking', 'lunas dp', 'konfirmasi', 'dimasak'])) {
            $this->addToGoogleCalendar($order);
        }

        return redirect()->back()->with('success', 'Status pesanan berhasil diperbarui!');
    }

    public function completePayment($id)
    {
        $order = Order::findOrFail($id);

        $order->update([
            'remaining_payment' => 0,
            'status'            => 'done', 
            'payment_status'    => 'settlement'
        ]);

        return redirect()->back()->with('success', 'Pelunasan COD sisa 70% berhasil dicatat! Pesanan resmi SELESAI.');
    }

    /**
     * FUNGSI LOGIKA PERBAIKAN: Mengambil token digital secara aman dari database
     */
    private function addToGoogleCalendar($order)
    {
        try {
            // Ambil data admin yang menyimpan token google calendar di database (Solusi Robot Webhook)
            $admin = User::whereNotNull('google_calendar_token')
                         ->where('google_calendar_token', '!=', 'null')
                         ->first();
            
            if (!$admin) {
                return;
            }

            $token = json_decode($admin->google_calendar_token, true);
            
            if (!is_array($token)) {
                return;
            }

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

            $orderWithItems = Order::with('items.menu')->find($order->id);
            $menuList = "";
            foreach ($orderWithItems->items as $item) {
                $menuList .= "- " . $item->menu->title . " (" . $item->quantity . "x)\n";
            }

            $startDateTime = $order->event_date . 'T' . $order->event_time; 
            $endDateTime = date('Y-m-d\TH:i:s', strtotime($startDateTime . ' +2 hours'));

            $buyerName = $order->recipient_name ?? ($order->user->name ?? 'Pelanggan Offline');

            $event = new Event([
                'summary' => '🍳 Jadwal Masak: Pesanan #' . ($order->order_number ?? $order->id) . ' - ' . $buyerName,
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
            \Log::error('Sistem mengabaikan Google Calendar karena Token Expired/Bermasalah: ' . $e->getMessage());
        }
    }
}