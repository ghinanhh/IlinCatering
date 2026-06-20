<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            // 🍕 REVISI: Hubungkan review ke tabel menus agar ulasan bisa dibuat per makanan
            if (!Schema::hasColumn('reviews', 'menu_id')) {
                $table->foreignId('menu_id')->nullable()->constrained('menus')->onDelete('cascade');
            }
            
            // 💬 REVISI: Tambahkan kolom untuk menampung teks balasan dari admin
            if (!Schema::hasColumn('reviews', 'admin_reply')) {
                $table->text('admin_reply')->nullable()->after('comment');
            }
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign(['menu_id']);
            $table->dropColumn(['menu_id', 'admin_reply']);
        });
    }
};