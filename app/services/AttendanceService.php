<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\DailyToken;
use App\Models\User;
use Carbon\Carbon;

/**
 * Service qui contient toute la logique métier du pointage.
 * Il gère :
 * - Le check-in (arrivée)
 * - Le check-out (départ)
 * - Le calcul des retards
 * - Le calcul des départs anticipés
 * - La validation des QR Codes
 */
class AttendanceService
{
    /**
     * Enregistre l'arrivée d'un employé.
     */
    public function checkIn(User $user, string $scannedToken): array
    {
        // Vérifie si le QR Code scanné existe et n'est pas expiré
        if (!DailyToken::isTokenValid($scannedToken)) {
            return ['success' => false, 'message' => 'QR code invalide ou expiré.'];
        }

        // Recherche un pointage déjà enregistré aujourd'hui
        $existing = $user->attendances()->whereDate('date', today())->first();

        // Empêche un deuxième check-in dans la même journée
        if ($existing && $existing->check_in) {
            return ['success' => false, 'message' => 'Vous avez déjà pointé aujourd\'hui.'];
        }

        // Heure actuelle
        $now = now();

        // Récupération des horaires de travail de l'employé
        $schedule = $this->resolveSchedule($user);

        // Statut par défaut
        $status = 'present';

        // Heure limite = heure d'arrivée + tolérance
        $deadline = $this->timeOnDate($schedule['start_time'], $now)
            ->addMinutes($schedule['tolerance_minutes']);

        // Si l'employé arrive après l'heure limite → retard
        if ($now->greaterThan($deadline)) {
            $status = 'late';
        }

        // Création ou mise à jour du pointage du jour
        $attendance = Attendance::updateOrCreate(
            ['user_id' => $user->id, 'date' => today()],
            ['check_in' => $now, 'status' => $status, 'method' => 'qr']
        );

        // Message affiché à l'utilisateur
        return [
            'success'    => true,
            'message'    => $status === 'late'
                ? 'Check-in enregistré — Retard détecté !'
                : 'Check-in enregistré !',
            'attendance' => $attendance,
        ];
    }

    /**
     * Enregistre le départ d'un employé.
     */
    public function checkOut(User $user, string $scannedToken): array
    {
        // Vérifie la validité du QR Code
        if (!DailyToken::isTokenValid($scannedToken)) {
            return ['success' => false, 'message' => 'QR code invalide ou expiré.'];
        }

        // Recherche le pointage du jour
        $attendance = $user->attendances()->whereDate('date', today())->first();

        // Impossible de faire un check-out sans check-in
        if (!$attendance || !$attendance->check_in) {
            return ['success' => false, 'message' => 'Aucun check-in trouvé.'];
        }

        // Vérifie si le départ a déjà été enregistré
        if ($attendance->check_out) {
            return ['success' => false, 'message' => 'Check-out déjà effectué.'];
        }

        // Heure actuelle
        $now = now();

        // Récupération des horaires de travail
        $schedule = $this->resolveSchedule($user);

        // Conserve le statut existant (présent ou retard)
        $status = $attendance->status;

        // Heure officielle de fin de travail
        $endTime = $this->timeOnDate($schedule['end_time'], $now);

        // Si l'employé part avant l'heure prévue
        if ($now->lessThan($endTime)) {
            $status = 'early_departure';
        }

        // Mise à jour du pointage
        $attendance->update([
            'check_out'    => $now,

            // Nombre total de minutes travaillées
            'hours_worked' => $attendance->check_in->diffInMinutes($now),

            // Nouveau statut
            'status'       => $status,
        ]);

        // Message affiché à l'utilisateur
        $message = match ($status) {

            // Départ avant l'heure prévue
            'early_departure' =>
                'Check-out enregistré — Départ anticipé détecté.',

            // Employé arrivé en retard mais a terminé sa journée
            'late' =>
                'Check-out enregistré. Bonne journée ! (retard à l\'arrivée)',

            // Cas normal
            default =>
                'Check-out enregistré. Bonne journée !',
        };

        return [
            'success'    => true,
            'message'    => $message,
            'attendance' => $attendance
        ];
    }

    /**
     * Récupère les horaires de travail.
     *
     * Priorité :
     * 1. Horaire du département
     * 2. Paramètres généraux définis par l'administrateur
     */
    private function resolveSchedule(User $user): array
    {
        // Recherche un horaire spécifique au département
        $departmentSchedule = $user->department?->todaySchedule();

        if ($departmentSchedule) {
            return [
                'start_time'        => $departmentSchedule->start_time,
                'end_time'          => $departmentSchedule->end_time,
                'tolerance_minutes' => (int) $departmentSchedule->tolerance_minutes,
            ];
        }

        // Sinon utilisation des paramètres globaux
        return [
            'start_time'        => config('attendance.fixed_arrival_time', '09:00'),
            'end_time'          => config('attendance.work_end_time', '17:00'),
            'tolerance_minutes' => (int) config('attendance.late_tolerance_minutes', 15),
        ];
    }

    /**
     * Transforme une heure (09:00 par exemple)
     * en objet Carbon avec la date du jour.
     *
     * Exemple :
     * 09:00 → 2026-06-23 09:00:00
     */
    private function timeOnDate(mixed $time, Carbon $date): Carbon
    {
        // Si l'heure est déjà un objet Carbon
        if ($time instanceof Carbon) {
            $timeString = $time->format('H:i:s');
        } else {

            // Conversion en chaîne de caractères
            $timeString = (string) $time;

            // Ajoute les secondes si elles n'existent pas
            if (strlen($timeString) === 5) {
                $timeString .= ':00';
            }
        }

        // Construit une date complète (date + heure)
        return Carbon::parse(
            $date->format('Y-m-d') . ' ' . $timeString
        );
    }
}