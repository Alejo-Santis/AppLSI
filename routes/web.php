<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\EmergencyContactController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SalaryHistoryController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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

    Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])
        ->name('password.request');

    // Enviar el enlace de recuperación
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])
        ->name('password.email');

    // Mostrar formulario de restablecimiento
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])
        ->name('password.reset');

    // Procesar el restablecimiento
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])
        ->name('password.update');
});

/*
|--------------------------------------------------------------------------
| Rutas Protegidas (Autenticadas)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('user')->group(function () {
        Route::get('/profile', [UserController::class, 'profile'])->name('user.profile');
        Route::post('/profile/update', [UserController::class, 'updateProfile'])->name('user.profile.update');
        Route::delete('/profile/avatar', [UserController::class, 'deleteAvatar'])->name('user.avatar.delete');
        Route::post('/info/update', [UserController::class, 'updateAccountInfo'])->name('user.updateInfo');
        Route::post('/password/update', [UserController::class, 'updatePassword'])->name('user.updatePassword');
        Route::get('/account', [UserController::class, 'account'])->name('user.account');
    });

    Route::prefix('departments')->group(function () {
        Route::get('/', [DepartmentController::class, 'departments'])->name('departments.all');
        Route::get('/create', [DepartmentController::class, 'createDepartment'])->name('departments.create');
        Route::post('/store', [DepartmentController::class, 'storeDepartment'])->name('departments.store');
        Route::get('/show/{id}', [DepartmentController::class, 'showDepartmentDetails'])->name('departments.show');
        Route::get('/edit/{id}', [DepartmentController::class, 'editDepartment'])->name('departments.edit');
        Route::put('/update/{id}', [DepartmentController::class, 'updateDeparment'])->name('departments.update');
        Route::delete('/delete/{id}', [DepartmentController::class, 'destroyDepartment'])->name('departments.delete');

        Route::get('/export', [DepartmentController::class, 'exportDepartments'])->name('departments.export');
        Route::post('/import', [DepartmentController::class, 'importDepartments'])->name('departments.import');
    });

    Route::prefix('positions')->group(function () {
        Route::get('/', [PositionController::class, 'positions'])->name('positions.all');
        Route::get('/create', [PositionController::class, 'createPosition'])->name('positions.create');
        Route::post('/store', [PositionController::class, 'storePosition'])->name('positions.store');
        Route::get('/show/{id}', [PositionController::class, 'showPositionDetails'])->name('positions.show');
        Route::get('/edit/{id}', [PositionController::class, 'editPosition'])->name('positions.edit');
        Route::put('/update/{id}', [PositionController::class, 'updatePosition'])->name('positions.update');
        Route::delete('/delete/{id}', [PositionController::class, 'destroyPosition'])->name('positions.delete');

        Route::post('/import', [PositionController::class, 'import'])->name('positions.import');
        Route::get('/export', [PositionController::class, 'export'])->name('positions.export');
    });

    Route::prefix('employees')->group(function () {
        Route::get('/', [EmployeeController::class, 'employees'])->name('employees.all');
        Route::get('/create', [EmployeeController::class, 'createEmployee'])->name('employees.create');
        Route::post('/store', [EmployeeController::class, 'storeEmployee'])->name('employees.store');
        Route::get('/show/{employee}', [EmployeeController::class, 'showEmployeeDetails'])->name('employees.show');
        Route::get('/edit/{employee}', [EmployeeController::class, 'editEmployee'])->name('employees.edit');
        Route::post('/update/{employee}', [EmployeeController::class, 'updateEmployee'])->name('employees.update');
        Route::delete('/delete/{employee}', [EmployeeController::class, 'destroyEmployee'])->name('employees.delete');
        Route::delete('/{employee}/photo', [EmployeeController::class, 'deletePhoto'])
            ->name('employees.delete-photo');

        Route::get('/export', [EmployeeController::class, 'exportEmployees'])->name('employees.export');
        Route::post('/import', [EmployeeController::class, 'importEmployees'])->name('employees.import');

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

        Route::prefix('{employee}/salary-history')->name('salary.')->group(function () {
            Route::get('/', [SalaryHistoryController::class, 'history'])->name('salaries.all');
            Route::post('/store', [SalaryHistoryController::class, 'storeHistory'])->name('salaries.store');
            Route::delete('{history}/delete', [SalaryHistoryController::class, 'destroyHistory'])->name('salaries.destroy');
        });

        // Ruta para obtener razones de cambio (fuera del grupo {employee})
        Route::get('/salary-change-reasons', [SalaryHistoryController::class, 'getChangeReasons'])->name('salary.reasons');
    });

    Route::prefix('projects')->name('projects.')->group(function () {
        Route::get('/', [ProjectController::class, 'projects'])->name('all');
        Route::get('/create', [ProjectController::class, 'createProject'])->name('create');
        Route::post('/store', [ProjectController::class, 'storeProject'])->name('store');
        Route::get('/show/{project}', [ProjectController::class, 'showProject'])->name('show');
        Route::get('/edit/{project}', [ProjectController::class, 'editProject'])->name('edit');
        Route::put('/update/{project}', [ProjectController::class, 'updateProject'])->name('update');
        Route::delete('/delete/{project}', [ProjectController::class, 'destroyProject'])->name('delete');

        // Asignaciones
        Route::post('/{project}/assign', [ProjectController::class, 'assignEmployee'])->name('projects.assign');
        Route::delete('/{project}/assignments/{assignment}/remove', [ProjectController::class, 'removeEmployee'])->name('projects.remove');

        Route::get('/export', [ProjectController::class, 'export'])->name('projects.export');
        Route::post('/import', [ProjectController::class, 'import'])->name('projects.import');
    });

    /*
    |--------------------------------------------------------------------------
    | Logout
    |--------------------------------------------------------------------------
    */
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Agregar estas rutas dentro del grupo middleware('auth')

Route::prefix('admin')->middleware(['auth', 'role:admin'])->name('admin.')->group(function () {

    // Gestión de Roles
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::get('/create', [RoleController::class, 'create'])->name('create');
        Route::post('/store', [RoleController::class, 'store'])->name('store');
        Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('edit');
        Route::put('/{role}', [RoleController::class, 'update'])->name('update');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy');
        Route::get('/{role}/permissions', [RoleController::class, 'permissions'])->name('permissions');
        Route::post('/{role}/permissions', [RoleController::class, 'syncPermissions'])->name('sync-permissions');
    });

    // Gestión de Usuarios
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::get('/{user}/roles', [RoleController::class, 'roles'])->name('userRoles');
        Route::post('/{user}/roles', [RoleController::class, 'syncRoles'])->name('syncUserRoles');
        Route::get('/{user}/permissions', [RoleController::class, 'permissions'])->name('userPermissions');
        Route::post('/{user}/permissions', [RoleController::class, 'syncPermissions'])->name('syncUserPermissions');
    });
});
