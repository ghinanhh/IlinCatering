<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name'];

    // Relasi: Satu kategori bisa dimiliki oleh banyak menu
    public function menus()
    {
        return $this->hasMany(Menu::class);
    }
}