<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer l'admin
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
                'phone' => '0612345678',
                'hire_date' => '2024-01-01',
            ]
        );

        // Créer des employés
        $employees = [
            [
                'name' => 'Ahamed Soniya ',
                'email' => 'soniya@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'employe',
                'is_active' => true,
                'phone' => '0612345679',
                'hire_date' => '2024-02-15',
            ],
            [
                'name' => 'Lihwane Ahmed',
                'email' => 'lihwane@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'employe',
                'is_active' => true,
                'phone' => '0612345680',
                'hire_date' => '2024-03-01',
            ],
            [
                'name' => 'Said Ahmed Roukia',
                'email' => 'roukia@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'employe',
                'is_active' => true,
                'phone' => '0612345681',
                'hire_date' => '2024-04-10',
            ],
            [
                'name' => 'Kamanta Kadidia Yeya',
                'email' => 'kamanta@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'employe',
                'is_active' => true,
                'phone' => '0612345682',
                'hire_date' => '2024-05-20',
            ],
            [
                'name' => 'Fayadi Taoufik',
                'email' => 'fayadi@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'employe',
                'is_active' => true,
                'phone' => '0612345683',
                'hire_date' => '2024-06-01',
            ],
            [
                'name' => 'Samuel TCHABLINTETE',
                'email' => 'samuel@gmail.com',  
                'password' => Hash::make('password'),
                'role' => 'employe',
                'is_active' => false,
                'phone' => '0612345684',
                'hire_date' => '2024-06-15',
            ],
        ];

        foreach ($employees as $employee) {
            User::firstOrCreate(
                ['email' => $employee['email']],
                $employee
            );
        }
    }
}
