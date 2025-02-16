<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\IndicatorController;
use App\Http\Controllers\IndicatorValueController;
use App\Http\Controllers\InspectionController;
use App\Http\Controllers\ProblemController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\SolutionController;
use App\Models\IndicatorValue;
use Illuminate\Support\Facades\Route;


Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'loginAction'])->name('loginAction');


Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index']);
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('equipment', EquipmentController::class);
    Route::resource('indicator', IndicatorController::class);
    Route::resource('problem', ProblemController::class);
    Route::resource('inspection', InspectionController::class)->except(['show']);
    Route::resource('solution', SolutionController::class)->except(['show']);
    Route::resource('result', ResultController::class)->except(['show']);
    Route::get('inspection/edit-matrix', [InspectionController::class, 'editMatrix'])->name('inspection.editMatrix');
    Route::put('inspection/update-matrix', [InspectionController::class, 'updateMatrix'])->name('inspection.updateMatrix');
   
    Route::get('solution/edit-matrix', [SolutionController::class, 'editMatrix'])->name('solution.editMatrix');
    Route::put('solution/update-matrix', [SolutionController::class, 'updateMatrix'])->name('solution.updateMatrix');
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});