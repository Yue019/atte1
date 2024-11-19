<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WorkController;


Route::middleware('auth')->group(function () {
    Route::get('/', [AuthController::class, 'index'])->name('home');
});

Route::post('/start-work', [WorkController::class, 'startWork'])->name('start-work');

Route::post('/end-work', [WorkController::class, 'endWork'])->name('end-work');

Route::post('/start-rest', [WorkController::class, 'startRest'])->name('start-rest');

Route::post('/end-rest', [WorkController::class, 'endRest'])->name('end-rest');

Route::get('/attendance/date', [WorkController::class, 'indexDate'])->name('attendance.date');

