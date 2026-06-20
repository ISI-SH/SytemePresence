<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'department_id', 'is_active'];
    protected $hidden   = ['password', 'remember_token'];
    protected $casts    = ['password' => 'hashed', 'is_active' => 'boolean'];

    public function department()    { return $this->belongsTo(Department::class); }
    public function attendances()   { return $this->hasMany(Attendance::class); }
    public function leaveRequests() { return $this->hasMany(LeaveRequest::class); }

    public function isAdmin(): bool    { return $this->role === 'admin'; }
    public function isEmployee(): bool { return $this->role === 'employee'; }

    public function todayAttendance(): ?Attendance {
        return $this->attendances()->whereDate('date', today())->first();
    }
}