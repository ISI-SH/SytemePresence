<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pointage;
use App\Models\QrCode;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class DashboardController extends Controller
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
    
    public function index()
    {
        $today = Carbon::today();

        // --- Recalculer les statuts basés sur l'heure fixe -------------------
        $pointagesToday = Pointage::whereDate('check_in', $today)->get();
        foreach ($pointagesToday as $pointage) {
            $newStatus = $this->isLate($pointage->check_in) ? 'En retard' : 'Présent';
            if ($pointage->status !== $newStatus) {
                $pointage->status = $newStatus;
                $pointage->save();
            }
        }

        // --- 4 cartes stats -------------------------------------------------
        $presentCount = Pointage::whereDate('check_in', $today)
            ->where('status', 'Présent')
            ->count();

        $lateCount = Pointage::whereDate('check_in', $today)
            ->where('status', 'En retard')
            ->count();

        $totalActiveEmployees = User::where('role', 'employe')
            ->where('is_active', true)
            ->count();

        $absentCount = $totalActiveEmployees - Pointage::whereDate('check_in', $today)
            ->whereIn('status', ['Présent', 'En retard', 'Départ anticipé'])
            ->distinct('user_id')
            ->count('user_id');

        $avgHours = Pointage::whereDate('check_in', $today)
            ->whereNotNull('hours_worked')
            ->avg('hours_worked');
        $avgHours = $avgHours ? round($avgHours, 2) : 0;

        // --- Tableau des présences du jour (tous employés actifs) -----------
        $employees = User::where('role', 'employe')
            ->where('is_active', true)
            ->get();

        $presencesData = [];
        $startOfWeek = Carbon::now()->startOfWeek(); // lundi
        $endOfWeek = Carbon::now()->endOfWeek();     // dimanche
        $daysPassed = max(1, min(5, $today->diffInDays($startOfWeek) + 1)); // jours ouvrés écoulés (max 5)

        foreach ($employees as $employee) {
            $pointageToday = Pointage::where('user_id', $employee->id)
                ->whereDate('check_in', $today)
                ->first();

            // Taux semaine pour cet employé (lundi à aujourd'hui)
            $presentDaysCount = Pointage::where('user_id', $employee->id)
                ->whereBetween('check_in', [$startOfWeek, $today])
                ->whereIn('status', ['Présent', 'En retard'])
                ->count();

            $weeklyRate = ($presentDaysCount / $daysPassed) * 100;
            $weeklyRate = round($weeklyRate);

            $presencesData[] = [
                'user'          => $employee,
                'check_in'      => $pointageToday ? Carbon::parse($pointageToday->check_in)->format('H:i') : null,
                'status'        => $pointageToday ? $pointageToday->status : 'Absent',
                'weekly_rate'   => $weeklyRate,
            ];
        }

        // --- Taux semaine global (graphique) --------------------------------
        $weeklyGlobalRates = [];
        for ($i = 0; $i < 5; $i++) {
            $day = $startOfWeek->copy()->addDays($i);
            if ($day->greaterThan($today)) {
                $weeklyGlobalRates[$day->isoFormat('ddd')] = null;
                continue;
            }
            $presentThatDay = Pointage::whereDate('check_in', $day)
                ->whereIn('status', ['Présent', 'En retard'])
                ->distinct('user_id')
                ->count('user_id');
            $rate = $totalActiveEmployees > 0 ? ($presentThatDay / $totalActiveEmployees) * 100 : 0;
            $weeklyGlobalRates[$day->isoFormat('ddd')] = round($rate);
        }

        // --- Taux de présence hebdomadaire global ---------------------------
        $totalPossibleAttendance = $totalActiveEmployees * 5; // 5 jours ouvrés
        $actualAttendance = Pointage::whereBetween('check_in', [$startOfWeek, $today])
            ->whereIn('status', ['Présent', 'En retard'])
            ->distinct('user_id')
            ->count('user_id');
        $weeklyAttendanceRate = $totalPossibleAttendance > 0 
            ? round(($actualAttendance / $totalPossibleAttendance) * 100, 2) 
            : 0;

        // --- Données pour graphique (7 derniers jours) ----------------------
        $last7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $last7Days[] = [
                'date' => $date->format('d/m'),
                'present' => Pointage::whereDate('check_in', $date)
                    ->where('status', 'Présent')
                    ->count(),
                'late' => Pointage::whereDate('check_in', $date)
                    ->where('status', 'En retard')
                    ->count(),
                'absent' => $totalActiveEmployees - Pointage::whereDate('check_in', $date)
                    ->whereIn('status', ['Présent', 'En retard'])
                    ->distinct('user_id')
                    ->count('user_id')
            ];
        }

        // --- QR Code du jour ------------------------------------------------
        $qrCode = QrCode::whereDate('date', $today)->first();
        $qrCodeSvg = null;
        if ($qrCode) {
            // Utiliser une API externe pour générer le QR code
            $qrCodeSvg = '<img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=' . urlencode($qrCode->token) . '" alt="QR Code">';
        }

        return view('admin.dashboard', compact(
            'presentCount',
            'lateCount',
            'absentCount',
            'avgHours',
            'presencesData',
            'weeklyGlobalRates',
            'weeklyAttendanceRate',
            'last7Days',
            'qrCode',
            'qrCodeSvg'
        ));
    }
}