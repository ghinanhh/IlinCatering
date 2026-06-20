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
        Schema::create('pesanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('kode_pesanan')->unique();
            $table->integer('total_harga');
            $table->integer('dp_nominal'); 
            $table->enum('status_pembayaran', ['pending', 'dp_lunas', 'lunas', 'expired'])->default('pending');
            $table->enum('status_pesanan', ['menunggu', 'dikonfirmasi', 'dimasak', 'dikirim', 'selesai'])->default('menunggu');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanans');
    }
};
