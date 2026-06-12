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
        // Eviter de créer un doublon si le job tourne deux fois
        $existe = DailyToken::whereDate('date', today())->first();
        if ($existe) return;

        DailyToken::create([
            'token'      => Str::random(64),
            'date'       => today(),
            'expires_at' => now()->endOfDay(), // expire à 23:59:59
        ]);
    }
}