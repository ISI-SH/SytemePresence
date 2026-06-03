<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pointage extends Model
{
    protected $fillable = ['user_id', 'check_in', 'check_out', 'status', 'hours_worked'];
    
    protected $casts = [
        'check_in' => 'datetime',
        'check_out' => 'datetime',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
