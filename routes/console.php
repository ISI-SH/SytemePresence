<?php

use App\Jobs\GenerateDailyToken;
use App\Jobs\MarkAbsences;
use Illuminate\Support\Facades\Schedule;

// Génère le QR du jour tous les jours à minuit
Schedule::job(new GenerateDailyToken)->dailyAt('00:00');

// Marque les absents tous les jours à 23h55
Schedule::job(new MarkAbsences)->dailyAt('23:55');