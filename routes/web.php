<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('kanban');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Kanban Board - Vista principal
    Route::get('/kanban', function () {
        return view('kanban');
    })->name('kanban');
    
    // System Logs - Auditoría del sistema
    Route::get('/logs', function () {
        return view('logs');
    })->middleware('can:view-logs')->name('logs');
    
    // Dashboard redirige al kanban
    Route::get('/dashboard', function () {
        return redirect()->route('kanban');
    })->name('dashboard');

    // Perfil de usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
