<?php

namespace App\Console\Commands;

use App\Models\Pointage;
use Carbon\Carbon;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

#[Signature('attendance:recalculate-status')]
#[Description('Recalculer tous les statuts de pointage selon la configuration actuelle')]
class RecalculateAttendanceStatus extends Command
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
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Récupération de tous les pointages...');
        $pointages = Pointage::all();
        
        $count = 0;
        $updated = 0;
        
        foreach ($pointages as $pointage) {
            $count++;
            $newStatus = $this->isLate($pointage->check_in) ? 'En retard' : 'Présent';
            
            if ($pointage->status !== $newStatus) {
                $pointage->status = $newStatus;
                $pointage->save();
                $updated++;
            }
            
            if ($count % 100 === 0) {
                $this->info("Traité : {$count} pointages...");
            }
        }
        
        $this->info("Terminé !");
        $this->info("Total pointages : {$count}");
        $this->info("Statuts mis à jour : {$updated}");
        
        return Command::SUCCESS;
    }
}
