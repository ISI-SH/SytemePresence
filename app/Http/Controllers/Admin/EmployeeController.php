<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'employe');
        
        // Recherche par nom ou email
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }
        
        $employees = $query->orderBy('created_at', 'desc')->paginate(10);
        
        return view('admin.employes.index', compact('employees'));
    }
    
    public function create()
    {
        return view('admin.employes.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'hire_date' => 'nullable|date',
            'photo' => 'nullable|image|max:2048',
        ]);
        
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'employe',
            'is_active' => true,
            'phone' => $request->phone,
            'hire_date' => $request->hire_date,
        ];
        
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('photos', 'public');
            $data['photo'] = $photoPath;
        }
        
        User::create($data);
        
        return redirect()->route('admin.employes.index')
            ->with('success', 'Employé créé avec succès.');
    }
    
    public function edit(User $employee)
    {
        return view('admin.employes.edit', compact('employee'));
    }
    
    public function update(Request $request, User $employee)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $employee->id,
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'hire_date' => 'nullable|date',
            'photo' => 'nullable|image|max:2048',
        ]);
        
        $employee->name = $request->name;
        $employee->email = $request->email;
        $employee->phone = $request->phone;
        $employee->hire_date = $request->hire_date;
        
        if ($request->filled('password')) {
            $employee->password = Hash::make($request->password);
        }
        
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('photos', 'public');
            $employee->photo = $photoPath;
        }
        
        $employee->save();
        
        return redirect()->route('admin.employes.index')
            ->with('success', 'Employé mis à jour avec succès.');
    }
    
    public function toggleStatus(User $employee)
    {
        $employee->is_active = !$employee->is_active;
        $employee->save();
        
        $status = $employee->is_active ? 'activé' : 'désactivé';
        return redirect()->route('admin.employes.index')
            ->with('success', "Employé $status avec succès.");
    }
    
    public function destroy(User $employee)
    {
        $employee->delete();
        
        return redirect()->route('admin.employes.index')
            ->with('success', 'Employé supprimé avec succès.');
    }
}
