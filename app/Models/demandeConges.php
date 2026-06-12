<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class demandeConges extends Model
{
    protected $fillable = ['user_id', 'start_date', 'end_date', 'reason', 'status', 'reviewed_by'];
}
