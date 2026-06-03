<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\demandeConges;

class LeaveController extends Controller
{
    public function index()
    {
        $demandes = demandeConges::with('user')
            ->where('status', 'en_attente')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('admin.demandes.index', compact('demandes'));
    }
    
    public function approve(Request $request, demandeConges $demande)
    {
        $demande->status = 'accepte';
        $demande->reviewed_by = auth()->id();
        $demande->save();
        
        return redirect()->route('admin.demandes.index')
            ->with('success', 'Demande de congé acceptée avec succès.');
    }
    
    public function reject(Request $request, demandeConges $demande)
    {
        $demande->status = 'refuse';
        $demande->reviewed_by = auth()->id();
        $demande->save();
        
        return redirect()->route('admin.demandes.index')
            ->with('success', 'Demande de congé refusée avec succès.');
    }
}
