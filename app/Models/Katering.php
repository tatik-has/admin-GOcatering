<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Katering extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'deskripsi',
        'gambar',
        'alamat',
        'rating',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'rating' => 'decimal:1',
    ];
}
