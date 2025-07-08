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
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn('kategori'); // Hapus kolom
            // ATAU jika ingin membuat nullable:
            // $table->string('kategori')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            // Tambahkan kembali kolom jika perlu untuk rollback
            // $table->string('kategori')->after('nama');
        });
    }
};
