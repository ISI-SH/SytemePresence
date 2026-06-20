<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model {
    protected $fillable = ['user_id', 'date', 'check_in', 'check_out', 'status', 'method', 'hours_worked'];
    protected $casts    = ['check_in' => 'datetime', 'check_out' => 'datetime', 'date' => 'date'];

    public function user() { return $this->belongsTo(User::class); }

    public function statusLabel(): string {
        return match($this->status) {
            'present'         => 'Présent',
            'late'            => 'Retard',
            'early_departure' => 'Départ anticipé',
            'absent'          => 'Absent',
            default           => ucfirst($this->status),
        };
    }

    public function statusBadgeClass(): string {
        return match($this->status) {
            'present'         => 'badge-present',
            'late'            => 'badge-late',
            'early_departure' => 'badge-early',
            'absent'          => 'badge-absent',
            default           => 'badge-default',
        };
    }

    public function hoursWorkedFormatted(): string {
        if (!$this->hours_worked) return '—';
        return intdiv($this->hours_worked, 60).'h'.str_pad($this->hours_worked % 60, 2, '0', STR_PAD_LEFT);
    }
}