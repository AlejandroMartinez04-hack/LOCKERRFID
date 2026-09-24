<?php

use App\Http\Controllers\Api\AsignacionLockerController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DispositivoController;
use App\Http\Controllers\Api\LecturaRfidController;
use App\Http\Controllers\Api\LockerController;
use App\Http\Controllers\Api\TarjetaRfidController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::apiResource('users', UserController::class);
    Route::apiResource('tarjetas-rfid', TarjetaRfidController::class);
    Route::apiResource('lockers', LockerController::class);
    Route::apiResource('asignaciones-lockers', AsignacionLockerController::class);
    Route::apiResource('dispositivos', DispositivoController::class);
    Route::apiResource('lecturas-rfid', LecturaRfidController::class);
});
