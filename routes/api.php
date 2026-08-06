<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes publiques
|--------------------------------------------------------------------------
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Routes protégées
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    // Inscription
    Route::get('/register', [AuthController::class, 'register']);
    //Connexion
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('auth:sanctum');

    // Déconnexion
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth:sanctum');
});