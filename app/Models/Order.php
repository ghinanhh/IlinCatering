<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'recipient_name',
        'phone_number',
        'address',
        'event_date',     // 🌟 SEKARANG SUDAH DIIZINKAN MASUK DATABASE
        'event_time',     // 🌟 SEKARANG SUDAH DIIZINKAN MASUK DATABASE
        'total_price',
        'status',
        'dp_amount',
        'remaining_payment',
        'snap_token',     
        'payment_status', 
    ];

    /**
     * Relasi ke User (Pemilik Pesanan)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Item Pesanan
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}