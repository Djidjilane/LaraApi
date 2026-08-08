<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes publiques
|--------------------------------------------------------------------------
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
 //Profil
Route::middleware('auth:sanctum')->get('/profile', [AuthController::class, 'profile']);

   // Reset Password
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
   //Forgot Password
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::get('/reset-password/{token}', function ($token) {
    return response()->json([
        'token' => $token
    ]);
})->name('password.reset');
/*
|--------------------------------------------------------------------------
| Routes protégées
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

 
    // Déconnexion
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth:sanctum');
});