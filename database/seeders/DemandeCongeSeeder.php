<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\demandeConges;
use App\Models\User;
use Carbon\Carbon;

class DemandeCongeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = User::where('role', 'employe')->where('is_active', true)->get();
        $admin = User::where('role', 'admin')->first();
        
        $reasons = [
            'Vacances personnelles',
            'Maladie',
            'Famille',
            'Déménagement',
            'Rendez-vous médical',
        ];
        
        // Créer quelques demandes de congés
        foreach ($employees as $index => $employee) {
            $startDate = Carbon::now()->addDays(rand(5, 20));
            $endDate = $startDate->copy()->addDays(rand(1, 5));
            
            $status = 'en_attente';
            $reviewedBy = null;
            
            // Alternance des statuts pour la démo
            if ($index % 3 === 0) {
                $status = 'accepte';
                $reviewedBy = $admin->id;
            } elseif ($index % 3 === 1) {
                $status = 'refuse';
                $reviewedBy = $admin->id;
            }
            
            demandeConges::create([
                'user_id' => $employee->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'reason' => $reasons[rand(0, count($reasons) - 1)],
                'status' => $status,
                'reviewed_by' => $reviewedBy,
            ]);
        }
    }
}
