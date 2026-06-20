<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            // Menghubungkan ke tabel users (siapa yang memberi review)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Menghubungkan ke tabel orders (penting untuk relasi data TA kamu)
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            
            // Menghubungkan ke tabel menus (makanan apa yang direview)
            $table->foreignId('menu_id')->constrained('menus')->onDelete('cascade');
            
            $table->integer('rating'); // Untuk menyimpan angka 1-5
            $table->text('comment');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};