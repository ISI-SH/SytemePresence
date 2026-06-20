<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use App\Exports\ExportPointages;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with('user');
        
        // Filtre par date
        if ($request->has('date_start') && $request->date_start) {
            $query->whereDate('date', '>=', $request->date_start);
        }
        
        if ($request->has('date_end') && $request->date_end) {
            $query->whereDate('date', '<=', $request->date_end);
        }
        
        // Filtre par employé
        if ($request->has('employee_id') && $request->employee_id) {
            $query->where('user_id', $request->employee_id);
        }
        
        // Filtre par statut
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        $pointages = $query->orderBy('date', 'desc')->orderBy('check_in', 'desc')->paginate(20);
        
        $employees = User::whereIn('role', ['employe', 'employee'])->orderBy('name')->get();
        
        return view('admin.pointages.index', compact('pointages', 'employees'));
    }
    
    public function export(Request $request)
    {
        $filters = [
            'date_start' => $request->date_start,
            'date_end' => $request->date_end,
            'employee_id' => $request->employee_id,
            'status' => $request->status,
        ];
        
        return Excel::download(new ExportPointages($filters), 'pointages_' . date('Y-m-d') . '.csv');
    }
}
