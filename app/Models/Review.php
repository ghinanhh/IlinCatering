<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    // REVISI FASE 3: Tambahkan admin_reply ke fillable agar sistem mengizinkan penyimpanan balasan admin
    protected $fillable = ['user_id', 'order_id', 'menu_id', 'rating', 'comment', 'image', 'user_title', 'admin_reply'];

    /**
     * Relasi ke User (Siapa yang memberi review)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Order (Review ini untuk pesanan yang mana)
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relasi ke Menu (Review ini untuk makanan apa)
     */
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }
}