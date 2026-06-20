<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    // Tambahkan baris ini (pendaftaran kolom yang boleh diisi)
    protected $fillable = [
        'user_id',
        'menu_id',
        'quantity',
        'notes',
    ];

    /**
     * Relasi ke tabel Menu
     * Supaya kita bisa ambil nama & harga menu dari tabel keranjang
     */
    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}