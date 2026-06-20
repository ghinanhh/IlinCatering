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
        'category', 
        'stock'
    ];
}
