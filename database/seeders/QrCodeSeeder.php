<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\QrCode;
use Carbon\Carbon;

class QrCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer des QR codes pour les 7 derniers jours et les 7 prochains jours
        for ($i = -7; $i <= 7; $i++) {
            $date = Carbon::now()->addDays($i);
            
            // Sauter le week-end
            if ($date->isWeekend()) {
                continue;
            }
            
            $token = 'QR-' . $date->format('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 8));
            $expiresAt = $date->copy()->setHour(23)->setMinute(59)->setSecond(59);
            
            QrCode::create([
                'token' => $token,
                'date' => $date,
                'expires_at' => $expiresAt,
            ]);
        }
    }
}
