<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pointage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;

class PointageSeeder extends Seeder
{
    /**
     * Déterminer si un employé est en retard basé sur l'heure fixe
     */
    private function isLate($checkInTime)
    {
        $fixedArrivalTime = Config::get('attendance.fixed_arrival_time', '09:00');
        $toleranceMinutes = (int) Config::get('attendance.late_tolerance_minutes', 15);
        
        $fixedTime = Carbon::parse($fixedArrivalTime);
        $checkIn = Carbon::parse($checkInTime);
        
        $fixedTime->addMinutes($toleranceMinutes);
        
        // Comparer seulement les heures, pas les dates
        $fixedTimeMinutes = $fixedTime->hour * 60 + $fixedTime->minute;
        $checkInMinutes = $checkIn->hour * 60 + $checkIn->minute;
        
        return $checkInMinutes > $fixedTimeMinutes;
    }
    
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = User::where('role', 'employe')->where('is_active', true)->get();
        $fixedArrivalTime = Config::get('attendance.fixed_arrival_time', '09:00');
        $workStartTime = Config::get('attendance.work_start_time', '08:00');
        $workEndTime = Config::get('attendance.work_end_time', '17:00');
        
        // Créer des pointages pour les 7 derniers jours
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            
            // Sauter le week-end
            if ($date->isWeekend()) {
                continue;
            }
            
            foreach ($employees as $employee) {
                // 80% de chance d'être présent
                if (rand(1, 10) <= 8) {
                    // Heure d'arrivée entre work_start_time et fixedArrivalTime + 30 minutes
                    $startHour = Carbon::parse($workStartTime)->hour;
                    $endHour = Carbon::parse($fixedArrivalTime)->hour + 1;
                    
                    $checkIn = $date->copy()->setHour(rand($startHour, $endHour))->setMinute(rand(0, 59));
                    $checkOut = $date->copy()->setHour(Carbon::parse($workEndTime)->hour)->setMinute(rand(0, 59));
                    
                    // Déterminer le statut basé sur l'heure fixe
                    $status = $this->isLate($checkIn) ? 'En retard' : 'Présent';
                    
                    $hoursWorked = $checkOut->diffInHours($checkIn);
                    
                    Pointage::create([
                        'user_id' => $employee->id,
                        'check_in' => $checkIn,
                        'check_out' => $checkOut,
                        'status' => $status,
                        'hours_worked' => $hoursWorked,
                    ]);
                }
            }
        }
    }
}
