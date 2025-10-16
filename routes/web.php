<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DepartmentController;
use Inertia\Inertia;
use Symfony\Component\Routing\Attribute\DeprecatedAlias;

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

    Route::prefix('departments')->group(function () {
        Route::get('/', [DepartmentController::class, 'departments'])->name('departments.all');
        Route::get('/create/department', [DepartmentController::class, 'createDepartment'])->name('departments.create');
        Route::post('/store/department', [DepartmentController::class, 'storeDepartment'])->name('departments.store');
        Route::get('/show/department/{id}', [DeprecatedAlias::class, 'showDepartmentDetails'])->name('departments.show');
        Route::get('/edit/department/{id}', [DepartmentController::class, 'editDepartment'])->name('departments.edit');
        Route::put('/update/department/{id}', [DepartmentController::class, 'updateDeparment'])->name('departments.update');
        Route::delete('/delete/department/{id}', [DepartmentController::class, 'destroyDepartment'])->name('departments.delete');
    });

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
