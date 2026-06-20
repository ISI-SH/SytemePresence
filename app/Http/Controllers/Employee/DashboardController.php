<?php
namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\DailyToken;
use App\Services\AttendanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller {

    public function __construct(private AttendanceService $attendanceService) {}

    public function index() {
        $user            = Auth::user()->load('department.schedules');
        $todayAttendance = $user->todayAttendance();
        $schedule = $user->department?->todaySchedule();
        if (!$schedule) {
            $schedule = (object) [
                'start_time'          => config('attendance.fixed_arrival_time', '09:00'),
                'end_time'            => config('attendance.work_end_time', '17:00'),
                'tolerance_minutes'   => (int) config('attendance.late_tolerance_minutes', 15),
            ];
        }
        DailyToken::ensureCurrent();
        $hasToken        = DailyToken::current() !== null;
        $history         = $user->attendances()->orderBy('date', 'desc')->limit(30)->get();
        $monthStats      = $user->attendances()
            ->whereMonth('date', now()->month)->whereYear('date', now()->year)
            ->selectRaw("COUNT(*) AS total, SUM(status='present') AS present,
                         SUM(status='late') AS late, SUM(status='absent') AS absent,
                         SUM(status='early_departure') AS early_departure,
                         ROUND(AVG(CASE WHEN hours_worked IS NOT NULL THEN hours_worked END)/60,1) AS avg_hours")
            ->first();
        $pendingLeaves = $user->leaveRequests()->where('status', 'pending')->count();

        return view('employee.dashboard', compact(
            'user','todayAttendance','schedule','hasToken','history','monthStats','pendingLeaves'
        ));
    }

    public function checkIn(Request $request) {
        $request->validate(['token' => 'required|string']);
        $result = $this->attendanceService->checkIn(Auth::user(), $request->token);
        return redirect()->route('employee.dashboard')
            ->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function checkOut(Request $request) {
        $request->validate(['token' => 'required|string']);
        $result = $this->attendanceService->checkOut(Auth::user(), $request->token);
        return redirect()->route('employee.dashboard')
            ->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function history(Request $request) {
        $query = Auth::user()->attendances()->orderBy('date', 'desc');
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('month')) {
            [$year, $month] = explode('-', $request->month);
            $query->whereYear('date', $year)->whereMonth('date', $month);
        }
        return view('employee.history', ['attendances' => $query->paginate(15)->withQueryString()]);
    }

    public function leaveIndex() {
        return view('employee.leaves.index', [
            'leaves' => Auth::user()->leaveRequests()->orderBy('created_at','desc')->paginate(10)
        ]);
    }

    public function leaveStore(Request $request) {
        $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'reason'     => 'required|string|min:10|max:500',
        ]);
        Auth::user()->leaveRequests()->create($request->only('start_date','end_date','reason'));
        return redirect()->route('employee.leaves.index')->with('success', 'Demande soumise avec succès.');
    }
}