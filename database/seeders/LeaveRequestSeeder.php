<?php

namespace Database\Seeders;

use App\Models\LeaveRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class LeaveRequestSeeder extends Seeder
{
    public function run(): void
    {
        $employees = User::whereIn('role', ['employe', 'employee'])->where('is_active', true)->get();
        $admin = User::where('role', 'admin')->first();

        $reasons = [
            'Vacances personnelles',
            'Maladie',
            'Famille',
            'Déménagement',
            'Rendez-vous médical',
        ];

        foreach ($employees as $index => $employee) {
            $startDate = Carbon::now()->addDays(rand(5, 20));
            $endDate = $startDate->copy()->addDays(rand(1, 5));

            $status = 'pending';
            $reviewedBy = null;

            if ($index % 3 === 0) {
                $status = 'approved';
                $reviewedBy = $admin?->id;
            } elseif ($index % 3 === 1) {
                $status = 'rejected';
                $reviewedBy = $admin?->id;
            }

            LeaveRequest::create([
                'user_id' => $employee->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'reason' => $reasons[rand(0, count($reasons) - 1)],
                'status' => $status,
                'reviewed_by' => $reviewedBy,
            ]);
        }
    }
}
