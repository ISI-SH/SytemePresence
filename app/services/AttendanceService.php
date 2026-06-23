<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\DailyToken;
use App\Models\User;
use Carbon\Carbon;

class AttendanceService
{
    public function checkIn(User $user, string $scannedToken): array
    {
        if (!DailyToken::isTokenValid($scannedToken)) {
            return ['success' => false, 'message' => 'QR code invalide ou expiré.'];
        }

        $existing = $user->attendances()->whereDate('date', today())->first();
        if ($existing && $existing->check_in) {
            return ['success' => false, 'message' => 'Vous avez déjà pointé aujourd\'hui.'];
        }

        $now = now();
        $schedule = $this->resolveSchedule($user);
        $status = 'present';

        $deadline = $this->timeOnDate($schedule['start_time'], $now)
            ->addMinutes($schedule['tolerance_minutes']);

        if ($now->greaterThan($deadline)) {
            $status = 'late';
        }

        $attendance = Attendance::updateOrCreate(
            ['user_id' => $user->id, 'date' => today()],
            ['check_in' => $now, 'status' => $status, 'method' => 'qr']
        );

        return [
            'success'    => true,
            'message'    => $status === 'late'
                ? 'Check-in enregistré — Retard détecté !'
                : 'Check-in enregistré !',
            'attendance' => $attendance,
        ];
    }

    public function checkOut(User $user, string $scannedToken): array
    {
        if (!DailyToken::isTokenValid($scannedToken)) {
            return ['success' => false, 'message' => 'QR code invalide ou expiré.'];
        }

        $attendance = $user->attendances()->whereDate('date', today())->first();
        if (!$attendance || !$attendance->check_in) {
            return ['success' => false, 'message' => 'Aucun check-in trouvé.'];
        }
        if ($attendance->check_out) {
            return ['success' => false, 'message' => 'Check-out déjà effectué.'];
        }

        $now = now();
        $schedule = $this->resolveSchedule($user);
        $status = $attendance->status;

        $endTime = $this->timeOnDate($schedule['end_time'], $now);

        if ($now->lessThan($endTime)) {
            $status = 'early_departure';
        }

        $attendance->update([
            'check_out'    => $now,
            'hours_worked' => $attendance->check_in->diffInMinutes($now),
            'status'       => $status,
        ]);

        $message = match ($status) {
            'early_departure' => 'Check-out enregistré — Départ anticipé détecté.',
            'late'            => 'Check-out enregistré. Bonne journée ! (retard à l\'arrivée)',
            default           => 'Check-out enregistré. Bonne journée !',
        };

        return ['success' => true, 'message' => $message, 'attendance' => $attendance];
    }

    /**
     * Horaires du département si disponibles, sinon paramètres globaux (page admin).
     */
    private function resolveSchedule(User $user): array
    {
        $departmentSchedule = $user->department?->todaySchedule();

        if ($departmentSchedule) {
            return [
                'start_time'        => $departmentSchedule->start_time,
                'end_time'          => $departmentSchedule->end_time,
                'tolerance_minutes' => (int) $departmentSchedule->tolerance_minutes,
            ];
        }

        return [
            'start_time'        => config('attendance.fixed_arrival_time', '09:00'),
            'end_time'          => config('attendance.work_end_time', '17:00'),
            'tolerance_minutes' => (int) config('attendance.late_tolerance_minutes', 15),
        ];
    }

    private function timeOnDate(mixed $time, Carbon $date): Carbon
    {
        if ($time instanceof Carbon) {
            $timeString = $time->format('H:i:s');
        } else {
            $timeString = (string) $time;
            if (strlen($timeString) === 5) {
                $timeString .= ':00';
            }
        }

        return Carbon::parse($date->format('Y-m-d') . ' ' . $timeString);
    }
}
