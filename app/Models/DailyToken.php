<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Modèle représentant le token quotidien utilisé
// pour générer et valider le QR Code de présence
class DailyToken extends Model {

    // Champs pouvant être enregistrés automatiquement
    protected $fillable = [
        'token',
        'date',
        'expires_at'
    ];

    // Conversion automatique des dates par Laravel
    protected $casts = [
        'expires_at' => 'datetime',
        'date'       => 'date'
    ];

    // Récupère le token valide du jour
    public static function today(): ?self {

        return static::whereDate('date', today())

            // Vérifie que le token n'est pas expiré
            ->where('expires_at', '>', now())

            // Retourne le premier résultat trouvé
            ->first();
    }

    // Vérifie si le token scanné par l'employé
    // correspond au token enregistré
    public function isValid(string $scanned): bool {

        return

            // Le token scanné doit être identique
            $this->token === $scanned

            // Et le token ne doit pas être expiré
            && $this->expires_at > now();
    }
}