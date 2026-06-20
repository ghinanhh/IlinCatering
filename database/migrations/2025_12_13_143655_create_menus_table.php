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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('image')->nullable(); // Menggunakan nama kolom 'image' sesuai keinginanmu
            $table->string('title');             // Menggunakan 'title' sesuai kodemu
            $table->text('description');
            $table->bigInteger('price');
            
            // Tambahkan kategori agar Admin bisa memfilter Nasi Box/Prasmanan/Snack
            $table->enum('category', ['box', 'prasmanan', 'snack'])->default('box');
            
            $table->integer('stock')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};