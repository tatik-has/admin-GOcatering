<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
    'nama',
    'gambar',
    'kategori_utama',
    'sub_kategori',
    'deskripsi',
    'harga',
    'status',
    'porsi',
    'durasi',
    'is_featured',
    'is_available',
    'menu_items',
    'min_order',
    'max_order',
    'menu_katering',
    'rating',
    'estimasi_waktu',
    'catatan_khusus',
];

    protected $casts = [
    'harga' => 'decimal:2',
    'is_featured' => 'boolean',
    'is_available' => 'boolean',
    'menu_items' => 'array',
    'rating' => 'decimal:1',
    'min_order' => 'integer',
    'max_order' => 'integer',
];



    // Scope untuk filter berdasarkan kategori utama
    public function scopeByKategoriUtama($query, $kategori)
    {
        return $query->where('kategori_utama', $kategori);
    }

    // Scope untuk menu yang tersedia
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)->where('status', 'tersedia');
    }

    // Scope untuk menu featured
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    // Accessor untuk format harga
    public function getFormattedHargaAttribute()
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }

}