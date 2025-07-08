<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_name',
        'customer_phone',
        'items',
        'request_note',
        'total_amount',
        'status',
        'delivery_address',
        // 'delivered_at',
        // 'payment_method',
    ];

    protected $casts = [
        'items' => 'array', // Penting agar 'items' otomatis di-decode dari JSON
        // 'delivered_at' => 'datetime',
    ];

    // Jika ada relasi dengan model User (untuk mengambil nama/telepon user yang login)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}