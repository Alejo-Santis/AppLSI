<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Rutas de Autenticación (Guest)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    // Mostrar formularios
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/login', [AuthController::class, 'showLogin']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');

    // Procesar formularios
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});

/*
|--------------------------------------------------------------------------
| Rutas Protegidas (Autenticadas)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        return Inertia::render('App/Dashboard', [
            'stats' => [
                'users' => 1234,
                'revenue' => 45678,
                'orders' => 890,
            ]
        ]);
    })->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Rutas UI Components
    |--------------------------------------------------------------------------
    */
    Route::prefix('ui')->name('ui.')->group(function () {
        Route::get('/buttons', fn() => Inertia::render('UI/Buttons'))->name('buttons');
    });

    /*

    /*
    |--------------------------------------------------------------------------
    | Logout
    |--------------------------------------------------------------------------
    */
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
