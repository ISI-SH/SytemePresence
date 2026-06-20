<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model {
    protected $fillable = ['user_id', 'start_date', 'end_date', 'reason', 'status', 'reviewed_by'];
    protected $casts    = ['start_date' => 'date', 'end_date' => 'date'];

    public function user()     { return $this->belongsTo(User::class); }
    public function reviewer() { return $this->belongsTo(User::class, 'reviewed_by'); }

    public function statusLabel(): string {
        return match($this->status) {
            'pending'  => 'En attente',
            'approved' => 'Accepté',
            'rejected' => 'Refusé',
            default    => ucfirst($this->status),
        };
    }

    public function statusBadgeClass(): string {
        return match($this->status) {
            'approved' => 'badge-present',
            'rejected' => 'badge-absent',
            default    => 'badge-pending',
        };
    }
}