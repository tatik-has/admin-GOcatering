<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            // Jika pesanan bisa dari tamu (non-login) atau dari user yang login
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('customer_name')->nullable(); // Untuk nama pelanggan jika tamu atau override
            $table->string('customer_phone')->nullable(); // Untuk nomor telepon pelanggan jika tamu atau override
            $table->json('items'); // Array JSON berisi detail menu: [{"menu_id": 1, "menu_name": "Ayam Rendang", "quantity": 1, "price": 18000, "unit": "porsi"}, ...]
            $table->text('request_note')->nullable(); // Untuk kolom 'Request'
            $table->decimal('total_amount', 10, 2);
            $table->string('status')->default('pending'); // 'pending', 'processing', 'completed', 'cancelled', 'delivered'
            $table->string('delivery_address'); // Untuk kolom 'Alamat'
            // $table->string('payment_method')->nullable(); // Contoh: 'cash_on_delivery', 'online_payment'
            // $table->timestamp('delivered_at')->nullable(); // Kapan pesanan benar-benar diantar

            $table->timestamps(); // created_at (Tanggal Pesanan) dan updated_at

            // Foreign key ke tabel users (jika diperlukan)
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}