<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\LeaveController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Employee\DashboardController as EmployeeDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Routes Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::prefix('employes')->name('employes.')->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])->name('index');
        Route::get('/create', [EmployeeController::class, 'create'])->name('create');
        Route::post('/', [EmployeeController::class, 'store'])->name('store');
        Route::get('/{employee}/edit', [EmployeeController::class, 'edit'])->name('edit');
        Route::put('/{employee}', [EmployeeController::class, 'update'])->name('update');
        Route::put('/{employee}/toggle', [EmployeeController::class, 'toggleStatus'])->name('toggle');
        Route::delete('/{employee}', [EmployeeController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('pointages')->name('pointages.')->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('index');
        Route::get('/export', [AttendanceController::class, 'export'])->name('export');
    });

    Route::prefix('demandes')->name('demandes.')->group(function () {
        Route::get('/', [LeaveController::class, 'index'])->name('index');
        Route::put('/{demande}/approve', [LeaveController::class, 'approve'])->name('approve');
        Route::put('/{demande}/reject', [LeaveController::class, 'reject'])->name('reject');
    });

    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::put('/', [SettingsController::class, 'update'])->name('update');
    });
});

// Routes Employé
Route::middleware(['auth', 'role:employe,employee'])
    ->prefix('employee')->name('employee.')
    ->group(function () {
        Route::get('/dashboard', [EmployeeDashboardController::class, 'index'])->name('dashboard');
        Route::post('/check-in', [EmployeeDashboardController::class, 'checkIn'])->name('checkin');
        Route::post('/check-out', [EmployeeDashboardController::class, 'checkOut'])->name('checkout');
        Route::get('/history', [EmployeeDashboardController::class, 'history'])->name('history');
        Route::get('/leaves', [EmployeeDashboardController::class, 'leaveIndex'])->name('leaves.index');
        Route::post('/leaves', [EmployeeDashboardController::class, 'leaveStore'])->name('leaves.store');
    });
