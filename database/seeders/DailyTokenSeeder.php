<?php

namespace Database\Seeders;

use App\Models\DailyToken;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DailyTokenSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = -7; $i <= 7; $i++) {
            $date = Carbon::now()->addDays($i);

            if ($date->isWeekend()) {
                continue;
            }

            $token = 'QR-' . $date->format('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 8));
            $expiresAt = $date->copy()->setHour(23)->setMinute(59)->setSecond(59);

            DailyToken::updateOrCreate([
                'date' => $date->toDateString(),
            ], [
                'token' => $token,
                'expires_at' => $expiresAt,
            ]);
        }
    }
}
