<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\DailyToken;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;

class DashboardController extends Controller
{
    private function employeeRoles(): array
    {
        return ['employe', 'employee'];
    }

    private function statusLabel(string $status): string
    {
        return match ($status) {
            'present' => 'Présent',
            'late' => 'En retard',
            'early_departure' => 'Départ anticipé',
            'absent' => 'Absent',
            default => ucfirst($status),
        };
    }
    
    public function index()
    {
        $today = Carbon::today();
        $employeeRoles = $this->employeeRoles();
        $attendanceStatuses = ['present', 'late', 'early_departure'];
        $presentStatuses = ['present', 'late'];

        $presentCount = Attendance::whereDate('date', $today)
            ->where('status', 'present')
            ->count();

        $lateCount = Attendance::whereDate('date', $today)
            ->where('status', 'late')
            ->count();

        $totalActiveEmployees = User::whereIn('role', $employeeRoles)
            ->where('is_active', true)
            ->count();

        $absentCount = $totalActiveEmployees - Attendance::whereDate('date', $today)
            ->whereIn('status', $attendanceStatuses)
            ->distinct('user_id')
            ->count('user_id');

        $avgHours = Attendance::whereDate('date', $today)
            ->whereNotNull('hours_worked')
            ->avg('hours_worked');
        $avgHours = $avgHours ? round($avgHours / 60, 2) : 0;

        $employees = User::whereIn('role', $employeeRoles)
            ->where('is_active', true)
            ->get();

        $presencesData = [];
        $startOfWeek = Carbon::now()->startOfWeek();
        $daysPassed = max(1, min(5, $startOfWeek->diffInWeekdays($today) + 1));

        foreach ($employees as $employee) {
            $attendanceToday = Attendance::where('user_id', $employee->id)
                ->whereDate('date', $today)
                ->first();

            $presentDaysCount = Attendance::where('user_id', $employee->id)
                ->whereBetween('date', [$startOfWeek->toDateString(), $today->toDateString()])
                ->whereIn('status', $presentStatuses)
                ->count();

            $weeklyRate = ($presentDaysCount / $daysPassed) * 100;
            $weeklyRate = round($weeklyRate);

            $presencesData[] = [
                'user'          => $employee,
                'check_in'      => $attendanceToday?->check_in?->format('H:i'),
                'status'        => $attendanceToday ? $this->statusLabel($attendanceToday->status) : 'Absent',
                'weekly_rate'   => $weeklyRate,
            ];
        }

        $weeklyGlobalRates = [];
        for ($i = 0; $i < 5; $i++) {
            $day = $startOfWeek->copy()->addDays($i);
            if ($day->greaterThan($today)) {
                $weeklyGlobalRates[$day->isoFormat('ddd')] = null;
                continue;
            }
            $presentThatDay = Attendance::whereDate('date', $day)
                ->whereIn('status', $presentStatuses)
                ->distinct('user_id')
                ->count('user_id');
            $rate = $totalActiveEmployees > 0 ? ($presentThatDay / $totalActiveEmployees) * 100 : 0;
            $weeklyGlobalRates[$day->isoFormat('ddd')] = round($rate);
        }

        $totalPossibleAttendance = $totalActiveEmployees * 5;
        $actualAttendance = Attendance::whereBetween('date', [$startOfWeek->toDateString(), $today->toDateString()])
            ->whereIn('status', $presentStatuses)
            ->count();
        $weeklyAttendanceRate = $totalPossibleAttendance > 0 
            ? round(($actualAttendance / $totalPossibleAttendance) * 100, 2) 
            : 0;

        $last7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $last7Days[] = [
                'date' => $date->format('d/m'),
                'present' => Attendance::whereDate('date', $date)
                    ->where('status', 'present')
                    ->count(),
                'late' => Attendance::whereDate('date', $date)
                    ->where('status', 'late')
                    ->count(),
                'absent' => $totalActiveEmployees - Attendance::whereDate('date', $date)
                    ->whereIn('status', $presentStatuses)
                    ->distinct('user_id')
                    ->count('user_id')
            ];
        }

        $qrCode = DailyToken::ensureCurrent();
        $qrCodeSvg = null;
        if ($qrCode) {
            $qrCodeSvg = '<img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=' . urlencode($qrCode->token) . '" alt="QR Code">';
        }

        return view('admin.dashboard', compact(
            'presentCount',
            'lateCount',
            'absentCount',
            'avgHours',
            'presencesData',
            'weeklyGlobalRates',
            'weeklyAttendanceRate',
            'last7Days',
            'qrCode',
            'qrCodeSvg'
        ));
    }

    public function currentQr()
    {
        $token = DailyToken::ensureCurrent();

        return response()->json([
            'token'           => $token->token,
            'valid_from'      => $token->valid_from->toIso8601String(),
            'expires_at'      => $token->expires_at->toIso8601String(),
            'seconds_left'    => $token->secondsUntilExpiry(),
            'qr_url'          => 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($token->token),
        ]);
    }
}