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
    $table->string('nama');
    $table->string('gambar')->nullable();           // path gambar menu
    $table->string('kategori');                     // kategori makanan (misal: Nasi Box, Snack, dll)
    $table->text('deskripsi')->nullable();          // deskripsi menu
    $table->decimal('harga', 10, 2);                // harga menu
    $table->enum('status', ['tersedia', 'habis']);  // status menu (tersedia / habis)
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
