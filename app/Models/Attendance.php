<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model {

    // Champs autorisés à être remplis automatiquement
    // lors de la création ou modification d'un pointage
    protected $fillable = [
        'user_id',
        'date',
        'check_in',
        'check_out',
        'status',
        'method',
        'hours_worked'
    ];

    // Conversion automatique des données
    // Laravel transforme ces champs en objets Date/DateTime
    protected $casts = [
        'check_in'  => 'datetime',
        'check_out' => 'datetime',
        'date'      => 'date'
    ];

    // Relation : un pointage appartient à un seul utilisateur
    public function user() {
        return $this->belongsTo(User::class);
    }

    // Convertit le statut stocké en base de données
    // en texte lisible pour l'interface
    public function statusLabel(): string {

        return match($this->status) {

            'present'         => 'Présent',

            'late'            => 'Retard',

            'early_departure' => 'Départ anticipé',

            'absent'          => 'Absent',

            default           => ucfirst($this->status),
        };
    }

    // Retourne la classe CSS correspondant au statut
    // pour afficher les badges avec différentes couleurs
    public function statusBadgeClass(): string {

        return match($this->status) {

            'present'         => 'badge-present',

            'late'            => 'badge-late',

            'early_departure' => 'badge-early',

            'absent'          => 'badge-absent',

            default           => 'badge-default',
        };
    }

    // Convertit la durée travaillée stockée en minutes
    // vers un format plus lisible (ex: 450 min => 7h30)
    public function hoursWorkedFormatted(): string {

        // Si aucune durée n'est enregistrée
        if (!$this->hours_worked) {
            return '—';
        }

        return intdiv($this->hours_worked, 60)
            .'h'.
            str_pad(
                $this->hours_worked % 60,
                2,
                '0',
                STR_PAD_LEFT
            );
    }
}