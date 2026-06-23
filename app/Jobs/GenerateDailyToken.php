<?php

namespace App\Jobs;

use App\Models\DailyToken;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Str;

class GenerateDailyToken implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        // Vérifie si un token existe déjà aujourd'hui
        // pour éviter la création de doublons
        $existe = DailyToken::whereDate('date', today())->first();

        // Si un token existe déjà, on arrête le traitement
        if ($existe) return;

        // Création du token du jour
        DailyToken::create([

            // Génération d'une chaîne aléatoire de 64 caractères
            // qui sera encodée dans le QR Code
            'token' => Str::random(64),

            // Date du jour
            'date' => today(),

            // Heure d'expiration du QR Code
            // (23h59min59s)
            'expires_at' => now()->endOfDay(),
        ]);
    }
}