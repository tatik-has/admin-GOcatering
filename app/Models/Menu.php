<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Menu extends Model
{
    use HasFactory;

    /**
     * Kolom yang bisa diisi (mass assignable)
     */
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

    /**
     * Tipe data untuk casting otomatis
     */
    protected $casts = [
        'harga' => 'decimal:2',
        'rating' => 'decimal:1',
        'is_featured' => 'boolean',
        'is_available' => 'boolean',
        'menu_items' => 'array',
        'min_order' => 'integer',
        'max_order' => 'integer',
    ];

    /**
     * Scope: Filter berdasarkan kategori_utama
     */
    public function scopeByKategoriUtama($query, string $kategori)
    {
        return $query->where('kategori_utama', $kategori);
    }

    /**
     * Scope: Hanya menu yang tersedia dan aktif
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)
                     ->where('status', 'tersedia');
    }

    /**
     * Scope: Menu yang ditandai sebagai featured
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Accessor: Format harga ke dalam format Rupiah
     */
    public function getFormattedHargaAttribute(): string
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }

    /**
     * Accessor: URL lengkap gambar (jika ada)
     */
    public function getImageUrlAttribute(): ?string
    {
        return $this->gambar ? asset('storage/' . $this->gambar) : null;
    }

    /**
     * Accessor: Tanggal dibuat dalam format lokal (optional)
     */
    public function getFormattedCreatedAtAttribute(): ?string
    {
        return $this->created_at
            ? $this->created_at->format('d M Y H:i')
            : null;
    }

    /**
     * Accessor: Menu items sebagai array nama item
     */
    public function getMenuItemNamesAttribute(): array
    {
        return collect($this->menu_items)->pluck('nama_item')->filter()->all();
    }
}
