<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('pemesanans', function (Blueprint $table) {
            // Hanya tambahkan foreign key jika kolomnya SUDAH ADA
            if (!Schema::hasColumn('pemesanans', 'user_id')) {
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
            } else {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            }

            if (!Schema::hasColumn('pemesanans', 'menu_id')) {
                $table->foreignId('menu_id')->constrained()->onDelete('cascade');
            } else {
                $table->foreign('menu_id')->references('id')->on('menus')->onDelete('cascade');
            }
        });
    }

    public function down(): void {
        Schema::table('pemesanans', function (Blueprint $table) {
            // drop foreign only if exists
            $table->dropForeign(['user_id']);
            $table->dropForeign(['menu_id']);

            // jangan drop kolom jika kolom dipakai di tempat lain
            // kamu bisa hapus ini jika tidak ingin menghapus kolom:
            // $table->dropColumn(['user_id', 'menu_id']);
        });
    }
};
