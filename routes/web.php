<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\LeaveController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Employee\DashboardController as EmployeeDashboardController;
use Illuminate\Support\Facades\Route;

// ==========================
// AUTHENTIFICATION
// ==========================

// Affiche la page de connexion
Route::get('/login', [AuthController::class, 'index'])->name('login');

// Traite les informations saisies dans le formulaire de connexion
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// Déconnexion de l'utilisateur
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// ==========================
// ROUTES ADMINISTRATEUR
// ==========================

// Middleware auth : utilisateur connecté
// Middleware role:admin : accès réservé aux administrateurs
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    // Tableau de bord administrateur
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])
        ->name('dashboard');


    // ==========================
    // Gestion des employés
    // ==========================
    Route::prefix('employes')->name('employes.')->group(function () {

        // Liste des employés
        Route::get('/', [EmployeeController::class, 'index'])->name('index');

        // Formulaire d'ajout
        Route::get('/create', [EmployeeController::class, 'create'])->name('create');

        // Enregistrement d'un nouvel employé
        Route::post('/', [EmployeeController::class, 'store'])->name('store');

        // Formulaire de modification
        Route::get('/{employee}/edit', [EmployeeController::class, 'edit'])->name('edit');

        // Mise à jour des informations
        Route::put('/{employee}', [EmployeeController::class, 'update'])->name('update');

        // Activation / Désactivation d'un employé
        Route::put('/{employee}/toggle', [EmployeeController::class, 'toggleStatus'])->name('toggle');

        // Suppression d'un employé
        Route::delete('/{employee}', [EmployeeController::class, 'destroy'])->name('destroy');
    });


    // ==========================
    // Gestion des présences
    // ==========================
    Route::prefix('pointages')->name('pointages.')->group(function () {

        // Liste des pointages
        Route::get('/', [AttendanceController::class, 'index'])->name('index');

        // Export des présences
        Route::get('/export', [AttendanceController::class, 'export'])->name('export');
    });


    // ==========================
    // Gestion des congés
    // ==========================
    Route::prefix('demandes')->name('demandes.')->group(function () {

        // Liste des demandes de congé
        Route::get('/', [LeaveController::class, 'index'])->name('index');

        // Validation d'une demande
        Route::put('/{demande}/approve', [LeaveController::class, 'approve'])->name('approve');

        // Refus d'une demande
        Route::put('/{demande}/reject', [LeaveController::class, 'reject'])->name('reject');
    });


    // ==========================
    // Paramètres système
    // ==========================
    Route::prefix('settings')->name('settings.')->group(function () {

        // Affichage des paramètres
        Route::get('/', [SettingsController::class, 'index'])->name('index');

        // Modification des paramètres
        Route::put('/', [SettingsController::class, 'update'])->name('update');
    });
});


// ==========================
// ROUTES EMPLOYÉ
// ==========================

// Authentification obligatoire
// Accès réservé aux employés
Route::middleware(['auth', 'role:employe,employee'])
    ->prefix('employee')
    ->name('employee.')
    ->group(function () {

        // Tableau de bord employé
        Route::get('/dashboard',
            [EmployeeDashboardController::class, 'index'])
            ->name('dashboard');

        // Enregistrement de l'arrivée après scan du QR Code
        Route::post('/check-in',
            [EmployeeDashboardController::class, 'checkIn'])
            ->name('checkin');

        // Enregistrement du départ
        Route::post('/check-out',
            [EmployeeDashboardController::class, 'checkOut'])
            ->name('checkout');

        // Historique des présences
        Route::get('/history',
            [EmployeeDashboardController::class, 'history'])
            ->name('history');

        // Liste des demandes de congé
        Route::get('/leaves',
            [EmployeeDashboardController::class, 'leaveIndex'])
            ->name('leaves.index');

        // Création d'une demande de congé
        Route::post('/leaves',
            [EmployeeDashboardController::class, 'leaveStore'])
            ->name('leaves.store');
    });