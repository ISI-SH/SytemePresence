<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model {
    protected $fillable = ['name', 'description'];

    public function users()    { return $this->hasMany(User::class); }
    public function schedules(){ return $this->hasMany(Schedule::class); }

    public function todaySchedule(): ?Schedule {
        $dow = now()->isoWeekday();
        return $this->schedules()
            ->where(function ($q) use ($dow) {
                $q->where('day_of_week', $dow)->orWhereNull('day_of_week');
            })
            ->orderByRaw('day_of_week IS NULL ASC')
            ->first();
    }
}