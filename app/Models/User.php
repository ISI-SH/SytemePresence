<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
<<<<<<< HEAD

class User extends Authenticatable {
    use Notifiable;
=======
use  App\Models\pointages;
use App\Models\demandeConges;
#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;
>>>>>>> 5b37850a57da6b3fe55ae84b8c85831ff2dfe4f9

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
<<<<<<< HEAD
}
=======
    // app/Models/User.php
public function pointages() {
    return $this->hasMany(Pointages::class);
}
public function demandesConges() {
    return $this->hasMany(DemandeConges::class);
}
}
>>>>>>> 5b37850a57da6b3fe55ae84b8c85831ff2dfe4f9
