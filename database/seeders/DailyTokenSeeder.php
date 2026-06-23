<?php

namespace Database\Seeders;

use App\Models\DailyToken;
use Illuminate\Database\Seeder;

class DailyTokenSeeder extends Seeder
{
    public function run(): void
    {
        DailyToken::ensureCurrent();
    }
}
