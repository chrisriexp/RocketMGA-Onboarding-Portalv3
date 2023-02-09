<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\OnboardingController;
use App\Http\Controllers\API\FileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/**Authentication Routes */
Route::middleware('auth:sanctum')->post('/token/validate', [AuthController::class, 'validateToken']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/resetPassword', [AuthController::class, 'resetPassword']);
Route::middleware('auth:sanctum')->get('/logout', [AuthController::class, 'logout']);

/**Onboarding Routes */
Route::middleware('auth:sanctum')->get('/onboarding/check', [OnboardingController::class, 'check']);
Route::middleware('auth:sanctum')->post('/onboarding/update', [OnboardingController::class, 'update']);

/**FIle Upload */
Route::middleware(['auth:sanctum', 'abilities:admin,superadmin,marketing'])->get('/files', [FileController::class, 'index']);
// Route::middleware('auth:sanctum')->get('/file', [FileController::class, 'getUser']);
Route::middleware('auth:sanctum')->post('/upload', [FileController::class, 'upload'])->name('upload');