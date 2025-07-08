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
        Schema::create('pemesanans', function (Blueprint $table) {
    $table->id();
    $table->string('nama_pelanggan');
    $table->string('pesanan'); // seperti: "Ayam Rendang + Tumis Kangkung"
    $table->text('request')->nullable(); // catatan permintaan tambahan
    $table->string('jumlah'); // bisa "2 porsi", "1 Paket"
    $table->decimal('total_harga', 10, 2);
    $table->date('tanggal_pesan');
    $table->string('alamat');
    $table->string('telepon');
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemesanans');
    }
};
