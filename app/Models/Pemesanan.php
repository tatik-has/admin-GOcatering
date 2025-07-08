<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemesanan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_pelanggan',
        'pesanan',
        'request',
        'jumlah',
        'total_harga',
        'tanggal_pesan',
        'alamat',
        'telepon',
        'user_id',  // tambahkan ini
        'menu_id',  // tambahkan ini
    ];

    // Relasi ke tabel users
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke tabel menus
    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}
