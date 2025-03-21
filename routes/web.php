<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\IndicatorController;
use App\Http\Controllers\InspectController;
use App\Http\Controllers\ProblemController;
use App\Http\Controllers\RuleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'loginAction'])->name('loginAction');

Route::middleware(['auth'])->group(function () {
    // Route yang bisa diakses semua user (dashboard & inspection)
    Route::get('/', [DashboardController::class, 'index']);
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('inspect', InspectController::class)->except(['show']);
    Route::post('/inspect/delete-all', [InspectController::class, 'deleteAll'])->name('inspect.deleteAll');
    Route::get('/inspect/print', [InspectController::class, 'printHistory'])->name('inspect.printHistory');
    Route::get('/inspect/export-excel', [InspectController::class, 'exportExcel'])->name('inspect.exportExcel');

    // Route yang hanya bisa diakses oleh admin
    Route::middleware(['admin'])->group(function () {
        Route::resource('equipment', EquipmentController::class);
        Route::resource('indicator', IndicatorController::class);
        Route::resource('problem', ProblemController::class);
        Route::resource('rules', RuleController::class);
        Route::resource('user', UserController::class);
        Route::post('/equipment/import-excel', [EquipmentController::class, 'importExcel'])->name('equipment.importExcel');
        Route::post('/indicator/import-excel', [IndicatorController::class, 'importExcel'])->name('indicator.importExcel');
        Route::post('/problem/import-excel', [ProblemController::class, 'importExcel'])->name('problem.importExcel');
    });

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
