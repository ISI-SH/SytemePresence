<?php

use App\Jobs\MarkAbsences;
use App\Jobs\RotateAttendanceToken;
use Illuminate\Support\Facades\Schedule;

// Génère un nouveau QR code chaque minute
Schedule::job(new RotateAttendanceToken)->everyMinute();

// Marque les absents tous les jours à 23h55
Schedule::job(new MarkAbsences)->dailyAt('23:55');