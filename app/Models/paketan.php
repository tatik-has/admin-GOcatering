<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class paketan extends Model
{
    use HasFactory;
            protected $table = 'paketan';


    protected $fillable = [
        'nama',
        'alamat',
        'harga',
        'deskripsi',
    ];
}
