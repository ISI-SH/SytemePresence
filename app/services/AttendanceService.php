<?php
namespace App\Services;

use App\Models\Attendance;
use App\Models\DailyToken;
use App\Models\User;
use Carbon\Carbon;

class AttendanceService {

    public function checkIn(User $user, string $scannedToken): array {
        $dailyToken = DailyToken::today();
        if (!$dailyToken || !$dailyToken->isValid($scannedToken))
            return ['success' => false, 'message' => 'QR code invalide ou expiré.'];

        $existing = $user->attendances()->whereDate('date', today())->first();
        if ($existing && $existing->check_in)
            return ['success' => false, 'message' => 'Vous avez déjà pointé aujourd\'hui.'];

        $now = now(); $status = 'present';
        $schedule = $user->department?->todaySchedule();
        if ($schedule) {
            $limite = Carbon::parse($schedule->start_time)->addMinutes($schedule->tolerance_minutes);
            if ($now->format('H:i:s') > $limite->format('H:i:s')) $status = 'late';
        }

        $attendance = Attendance::updateOrCreate(
            ['user_id' => $user->id, 'date' => today()],
            ['check_in' => $now, 'status' => $status, 'method' => 'qr']
        );

        return [
            'success'    => true,
            'message'    => $status === 'late' ? 'Check-in enregistré — Retard détecté !' : 'Check-in enregistré !',
            'attendance' => $attendance,
        ];
    }

    public function checkOut(User $user): array {
        $attendance = $user->attendances()->whereDate('date', today())->first();
        if (!$attendance || !$attendance->check_in)
            return ['success' => false, 'message' => 'Aucun check-in trouvé.'];
        if ($attendance->check_out)
            return ['success' => false, 'message' => 'Check-out déjà effectué.'];

        $now = now();
        $status = $attendance->status;
        $schedule = $user->department?->todaySchedule();
        if ($schedule && $now->format('H:i:s') < $schedule->end_time) $status = 'early_departure';

        $attendance->update([
            'check_out'    => $now,
            'hours_worked' => $attendance->check_in->diffInMinutes($now),
            'status'       => $status,
        ]);

        return ['success' => true, 'message' => 'Check-out enregistré. Bonne journée !', 'attendance' => $attendance];
    }
}