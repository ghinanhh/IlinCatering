<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Kolom untuk menyimpan token dari Midtrans
            $table->string('snap_token')->nullable()->after('id');
            
            // Kolom untuk memantau status pembayaran (pending, success, dll)
            $table->string('payment_status')->default('pending')->after('snap_token');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['snap_token', 'payment_status']);
        });
    }
};