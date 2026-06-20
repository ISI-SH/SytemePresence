<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model {
    protected $fillable = ['department_id', 'start_time', 'end_time', 'tolerance_minutes', 'day_of_week'];

    public function department() { return $this->belongsTo(Department::class); }
}