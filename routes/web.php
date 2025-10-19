<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\EmergencyContactController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PositionController;
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
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('departments')->group(function () {
        Route::get('/', [DepartmentController::class, 'departments'])->name('departments.all');
        Route::get('/create', [DepartmentController::class, 'createDepartment'])->name('departments.create');
        Route::post('/store', [DepartmentController::class, 'storeDepartment'])->name('departments.store');
        Route::get('/show/{id}', [DepartmentController::class, 'showDepartmentDetails'])->name('departments.show');
        Route::get('/edit/{id}', [DepartmentController::class, 'editDepartment'])->name('departments.edit');
        Route::put('/update/{id}', [DepartmentController::class, 'updateDeparment'])->name('departments.update');
        Route::delete('/delete/{id}', [DepartmentController::class, 'destroyDepartment'])->name('departments.delete');
    });

    Route::prefix('positions')->group(function () {
        Route::get('/', [PositionController::class, 'positions'])->name('positions.all');
        Route::get('/create', [PositionController::class, 'createPosition'])->name('positions.create');
        Route::post('/store', [PositionController::class, 'storePosition'])->name('positions.store');
        Route::get('/show/{id}', [PositionController::class, 'showPositionDetails'])->name('positions.show');
        Route::get('/edit/{id}', [PositionController::class, 'editPosition'])->name('positions.edit');
        Route::put('/update/{id}', [PositionController::class, 'updatePosition'])->name('positions.update');
        Route::delete('/delete/{id}', [PositionController::class, 'destroyPosition'])->name('positions.delete');
    });

    Route::prefix('employees')->group(function () {
        Route::get('/', [EmployeeController::class, 'employees'])->name('employees.all');
        Route::get('/create', [EmployeeController::class, 'createEmployee'])->name('employees.create');
        Route::post('/store', [EmployeeController::class, 'storeEmployee'])->name('employees.store');
        Route::get('/show/{employee}', [EmployeeController::class, 'showEmployeeDetails'])->name('employees.show');
        Route::get('/edit/{employee}', [EmployeeController::class, 'editEmployee'])->name('employees.edit');
        Route::put('/update/{employee}', [EmployeeController::class, 'updateEmployee'])->name('employees.update');
        Route::delete('/delete/{employee}', [EmployeeController::class, 'destroyEmployee'])->name('employees.delete');
        Route::delete('/{employee}/photo', [EmployeeController::class, 'deletePhoto'])
            ->name('employees.delete-photo');

        Route::get('/document-types', [DocumentController::class, 'getTypes'])->name('document.types');

        Route::prefix('/{employee}/documents')->name('employees.documents.')->group(function () {
            Route::get('/', [DocumentController::class, 'documents'])->name('documents.all');
            Route::post('/store', [DocumentController::class, 'storeDocument'])->name('documents.store');
            Route::get('/{document}/download', [DocumentController::class, 'downloadDocument'])->name('documents.download');
            Route::get('/{document}/preview', [DocumentController::class, 'previewDocument'])->name('documents.preview');
            Route::delete('/{document}/delete', [DocumentController::class, 'destroyDocument'])->name('documents.destroy');
        });

        Route::prefix('{employee}/contacts')->name('contacts.')->group(function () {
            Route::get('/', [EmergencyContactController::class, 'contacts'])->name('contacts.all');
            Route::post('/store', [EmergencyContactController::class, 'storeContact'])->name('contacts.store');
            Route::put('{contact}/update', [EmergencyContactController::class, 'updateContact'])->name('contacts.update');
            Route::delete('{contact}/delete', [EmergencyContactController::class, 'destroyContact'])->name('contacts.destroy');
        });

        // Ruta para obtener tipos de relación (fuera del grupo {employee})
        Route::get('/relationship-types', [EmergencyContactController::class, 'getRelationshipTypes'])->name('relationship.types');
    });

    /*
    |--------------------------------------------------------------------------
    | Logout
    |--------------------------------------------------------------------------
    */
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
