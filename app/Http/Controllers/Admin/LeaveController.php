<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function index()
    {
        $demandes = LeaveRequest::with('user')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('admin.demandes.index', compact('demandes'));
    }
    
    public function approve(Request $request, LeaveRequest $demande)
    {
        $demande->status = 'approved';
        $demande->reviewed_by = auth()->id();
        $demande->save();
        
        return redirect()->route('admin.demandes.index')
            ->with('success', 'Demande de congé acceptée avec succès.');
    }
    
    public function reject(Request $request, LeaveRequest $demande)
    {
        $demande->status = 'rejected';
        $demande->reviewed_by = auth()->id();
        $demande->save();
        
        return redirect()->route('admin.demandes.index')
            ->with('success', 'Demande de congé refusée avec succès.');
    }
}
