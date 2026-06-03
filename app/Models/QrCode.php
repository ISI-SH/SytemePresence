<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QrCode extends Model
{
    protected $fillable = ['token', 'date', 'expires_at'];
    
    protected $casts = [
        'date' => 'date',
        'expires_at' => 'datetime',
    ];
}
