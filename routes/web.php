<?php

    use App\Http\Controllers\Web\DashboardController;
    use Illuminate\Support\Facades\Route;

// Маршруты авторизации
    Route::get('/login', [DashboardController::class, 'showLogin'])->name('login');
    Route::post('/login', [DashboardController::class, 'login']);
    Route::post('/logout', [DashboardController::class, 'logout'])->name('logout');

// Защищенный маршрут панели статистики
    Route::middleware(['auth'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/', function () { return redirect()->route('dashboard'); });
    });

