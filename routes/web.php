<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VisitorController;
use Illuminate\Support\Facades\Route;
use App\Models\Employee;

Route::get('/', [FrontendController::class, 'index'])->name('home');
Route::post('/visitor', [FrontendController::class, 'store'])->name('visitor.store');
Route::get('camera', function () {
    return view('webcam');
});

Route::get('/dashboard', [DashboardController::class, "index"])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    //Departments Route
    Route::get('departments', [App\Http\Controllers\DepartmentController::class, 'index'])->name('departments.index');
    Route::post('departments', [App\Http\Controllers\DepartmentController::class, 'store'])->name('departments.store');
    Route::get('departments/{id}', [App\Http\Controllers\DepartmentController::class, 'edit'])->name('departments.edit');
    Route::PUT('departments/{id}', [App\Http\Controllers\DepartmentController::class, 'update'])->name('departments.update');
    Route::delete('departments/{id}', [App\Http\Controllers\DepartmentController::class, 'destroy'])->name('departments.destroy');

    //Employees Route

    Route::get('employees', [App\Http\Controllers\EmployeeController::class, 'index'])->name('employees.index');
    Route::get('employees/create', [App\Http\Controllers\EmployeeController::class, 'create'])->name('employees.create');
    Route::post('employees', [App\Http\Controllers\EmployeeController::class, 'store'])->name('employees.store');
    Route::get('employees/{id}', [App\Http\Controllers\EmployeeController::class, 'show'])->name('employees.show');
    Route::get('employees/{id}/edit', [App\Http\Controllers\EmployeeController::class, 'edit'])->name('employees.edit');
    Route::PUT('employees/{id}', [App\Http\Controllers\EmployeeController::class, 'update'])->name('employees.update');
    Route::post('employees/status/{id}', [App\Http\Controllers\EmployeeController::class, 'updateStatus'])->name('employees.status');
    Route::delete('employees/{id}', [App\Http\Controllers\EmployeeController::class, 'destroy'])->name('employees.destroy');

    Route::resource('visitors', VisitorController::class);

    Route::get('pending-visitors', [VisitorController::class, "pending"])->name("visitors.pending");

    Route::post('visitors/checkout/{id}', [App\Http\Controllers\VisitorController::class, 'checkout'])->name('visitors.checkout');
});

require __DIR__ . '/auth.php';
