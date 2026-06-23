<?php

namespace App\Jobs;

use App\Models\DailyToken;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class RotateAttendanceToken implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        DailyToken::ensureCurrent();

        DailyToken::where('expires_at', '<', now()->subDay())->delete();
    }
}
