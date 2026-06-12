<?php

namespace App\Jobs;

use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class MarkAbsences implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        // Récupérer tous les employés actifs
        $employes = User::where('role', 'employee')
            ->where('is_active', true)
            ->get();

        foreach ($employes as $employe) {
            // Déjà un enregistrement aujourd'hui ? → passer
            $existe = $employe->attendances()->whereDate('date', today())->first();
            if ($existe) continue;

            // En congé approuvé aujourd'hui ? → passer
            $enConge = $employe->leaveRequests()
                ->where('status', 'approved')
                ->whereDate('start_date', '<=', today())
                ->whereDate('end_date', '>=', today())
                ->exists();
            if ($enConge) continue;

            // Marquer absent
            Attendance::create([
                'user_id' => $employe->id,
                'date'    => today(),
                'status'  => 'absent',
                'method'  => 'auto',
            ]);
        }
    }
}