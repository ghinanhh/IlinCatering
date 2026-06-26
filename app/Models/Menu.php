<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    // Kolom yang boleh diisi manual
    protected $fillable = [
        'image', 
        'title', 
        'description', 
        'price', 
        'category_id', // 🌟 Diubah dari 'category' menjadi 'category_id'
        'category',    // Tetap kita biarkan sementara agar codingan lama tidak pecah/error
        'stock'
    ];

    // 🌟 Relasi: Menu menginduk ke tabel Category (Tabel ke-7)
    public function categoryRelation()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}