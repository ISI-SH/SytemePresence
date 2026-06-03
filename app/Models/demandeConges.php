<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class demandeConges extends Model
{
    protected $table = 'demandes_conges';
    
    protected $fillable = ['user_id', 'start_date', 'end_date', 'reason', 'status', 'reviewed_by'];
    
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
