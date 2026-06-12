<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class qrCodes extends Model
{
    protected $fillable = ['token', 'date', 'expires_at'];
}
