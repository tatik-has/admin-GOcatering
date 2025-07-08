<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->string('kategori_utama')->nullable();
            $table->string('sub_kategori')->nullable();
            $table->string('harga_promo')->nullable();
            $table->integer('porsi')->default(1);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_available')->default(true);
            $table->float('rating', 2, 1)->default(0); // rating contoh skala 0.0 - 99.9
            $table->string('estimasi_waktu')->nullable();
            $table->text('catatan_khusus')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn([
                'kategori_utama',
                'sub_kategori',
                'harga_promo',
                'porsi',
                'is_featured',
                'is_available',
                'rating',
                'estimasi_waktu',
                'catatan_khusus',
            ]);
        });
    }
};
