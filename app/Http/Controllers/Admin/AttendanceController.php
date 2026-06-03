<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pointage;
use App\Models\User;
use App\Exports\ExportPointages;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Pointage::with('user');
        
        // Filtre par date
        if ($request->has('date_start') && $request->date_start) {
            $query->whereDate('check_in', '>=', $request->date_start);
        }
        
        if ($request->has('date_end') && $request->date_end) {
            $query->whereDate('check_in', '<=', $request->date_end);
        }
        
        // Filtre par employé
        if ($request->has('employee_id') && $request->employee_id) {
            $query->where('user_id', $request->employee_id);
        }
        
        // Filtre par statut
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        $pointages = $query->orderBy('check_in', 'desc')->paginate(20);
        
        // Récupérer tous les employés pour le filtre
        $employees = User::where('role', 'employe')->orderBy('name')->get();
        
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
