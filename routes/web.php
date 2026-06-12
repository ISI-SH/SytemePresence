<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Employee\DashboardController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'role:employee'])
    ->prefix('employee')->name('employee.')
    ->group(function () {
        Route::get('/dashboard',  [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/check-in',  [DashboardController::class, 'checkIn'])->name('checkin');
        Route::post('/check-out', [DashboardController::class, 'checkOut'])->name('checkout');
        Route::get('/history',    [DashboardController::class, 'history'])->name('history');
        Route::get('/leaves',     [DashboardController::class, 'leaveIndex'])->name('leaves.index');
        Route::post('/leaves',    [DashboardController::class, 'leaveStore'])->name('leaves.store');
    });
// ── LOGIN TEMPORAIRE (à remplacer par tts) ──────────────────
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function () {
    $credentials = request()->only('email', 'password');
    if (auth()->attempt($credentials)) {
        request()->session()->regenerate();
        return auth()->user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('employee.dashboard');
    }
    return back()->withErrors(['email' => 'Identifiants incorrects.']);
});

Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');
// ────────────────────────────────────────────────────────────

// Routes Employé


Route::middleware(['auth', 'role:employee'])
    ->prefix('employee')->name('employee.')
    ->group(function () {
        Route::get('/dashboard',  [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/check-in',  [DashboardController::class, 'checkIn'])->name('checkin');
        Route::post('/check-out', [DashboardController::class, 'checkOut'])->name('checkout');
        Route::get('/history',    [DashboardController::class, 'history'])->name('history');
        Route::get('/leaves',     [DashboardController::class, 'leaveIndex'])->name('leaves.index');
        Route::post('/leaves',    [DashboardController::class, 'leaveStore'])->name('leaves.store');
    });
    