<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;

class AttendanceSeeder extends Seeder
{
    private function isLate($checkInTime): bool
    {
        $fixedArrivalTime = Config::get('attendance.fixed_arrival_time', '09:00');
        $toleranceMinutes = (int) Config::get('attendance.late_tolerance_minutes', 15);

        $fixedTime = Carbon::parse($fixedArrivalTime)->addMinutes($toleranceMinutes);
        $checkIn = Carbon::parse($checkInTime);

        $fixedTimeMinutes = $fixedTime->hour * 60 + $fixedTime->minute;
        $checkInMinutes = $checkIn->hour * 60 + $checkIn->minute;

        return $checkInMinutes > $fixedTimeMinutes;
    }

    public function run(): void
    {
        $employees = User::whereIn('role', ['employe', 'employee'])->where('is_active', true)->get();
        $fixedArrivalTime = Config::get('attendance.fixed_arrival_time', '09:00');
        $workStartTime = Config::get('attendance.work_start_time', '08:00');
        $workEndTime = Config::get('attendance.work_end_time', '17:00');

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);

            if ($date->isWeekend()) {
                continue;
            }

            foreach ($employees as $employee) {
                if (rand(1, 10) <= 8) {
                    $startHour = Carbon::parse($workStartTime)->hour;
                    $endHour = Carbon::parse($fixedArrivalTime)->hour + 1;

                    $checkIn = $date->copy()->setHour(rand($startHour, $endHour))->setMinute(rand(0, 59));
                    $checkOut = $date->copy()->setHour(Carbon::parse($workEndTime)->hour)->setMinute(rand(0, 59));
                    $status = $this->isLate($checkIn) ? 'late' : 'present';
                    $hoursWorked = $checkOut->diffInMinutes($checkIn);

                    Attendance::updateOrCreate([
                        'user_id' => $employee->id,
                        'date' => $date->toDateString(),
                    ], [
                        'check_in' => $checkIn,
                        'check_out' => $checkOut,
                        'status' => $status,
                        'method' => 'seed',
                        'hours_worked' => $hoursWorked,
                    ]);
                }
            }
        }
    }
}
