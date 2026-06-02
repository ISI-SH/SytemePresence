<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\LeaveController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login',[AuthController::class,'index'])->name('login');

Route::post('/login',[AuthController::class,'login'])->name('login.post');
Route::post('/logout',[AuthController::class,'logout'])->name('logout');

// Routes Admin protégées par middleware auth et role:admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Gestion des employés
    Route::prefix('employes')->name('employes.')->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])->name('index');
        Route::get('/create', [EmployeeController::class, 'create'])->name('create');
        Route::post('/', [EmployeeController::class, 'store'])->name('store');
        Route::get('/{employee}/edit', [EmployeeController::class, 'edit'])->name('edit');
        Route::put('/{employee}', [EmployeeController::class, 'update'])->name('update');
        Route::put('/{employee}/toggle', [EmployeeController::class, 'toggleStatus'])->name('toggle');
        Route::delete('/{employee}', [EmployeeController::class, 'destroy'])->name('destroy');
    });
    
    // Historique des pointages
    Route::prefix('pointages')->name('pointages.')->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('index');
        Route::get('/export', [AttendanceController::class, 'export'])->name('export');
    });
    
    // Gestion des demandes de congé
    Route::prefix('demandes')->name('demandes.')->group(function () {
        Route::get('/', [LeaveController::class, 'index'])->name('index');
        Route::put('/{demande}/approve', [LeaveController::class, 'approve'])->name('approve');
        Route::put('/{demande}/reject', [LeaveController::class, 'reject'])->name('reject');
    });
});