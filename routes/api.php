<?php

use App\Http\Controllers\API\ApprovalController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\OnboardingController;
use App\Http\Controllers\API\FileController;
use App\Http\Controllers\API\NotificationController;
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

Route::middleware(['auth:sanctum', 'ability:admin,super-admin,marketing'])->get('/user', function (Request $request) {
    return $request->user();
});

/**Authentication Routes */
Route::middleware('auth:sanctum')->post('/token/validate', [AuthController::class, 'validateToken']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/resetPassword', [AuthController::class, 'resetPassword']);
Route::post('/resetEmail', [AuthController::class, 'resetEmail']);
Route::middleware('auth:sanctum')->get('/logout', [AuthController::class, 'logout']);

/**Onboarding Routes */
Route::middleware('auth:sanctum')->get('/onboarding/check', [OnboardingController::class, 'check']);
Route::middleware('auth:sanctum')->post('/onboarding/update', [OnboardingController::class, 'update']);
Route::middleware(['auth:sanctum', 'ability:admin,super-admin,marketing'])->get('/onboarding/{filter?}', [OnboardingController::class, 'index']);
Route::middleware(['auth:sanctum', 'ability:admin,super-admin,marketing'])->post('/onboarding', [OnboardingController::class, 'user']);

/**FIle Upload */
Route::middleware(['auth:sanctum', 'ability:admin,super-admin,marketing'])->get('/files', [FileController::class, 'index']);
Route::middleware(['auth:sanctum', 'ability:admin,super-admin,marketing'])->get('/file/{id}', [FileController::class, 'file']);
Route::middleware('auth:sanctum')->post('/upload', [FileController::class, 'upload'])->name('upload');

/**Notifications */
Route::middleware('auth:sanctum')->post('/notification', [NotificationController::class, 'add']);
Route::middleware(['auth:sanctum', 'ability:admin,super-admin,marketing'])->get('/notification', [NotificationController::class, 'index']);
Route::middleware(['auth:sanctum', 'ability:admin,super-admin,marketing'])->post('/notification/read', [NotificationController::class, 'read']);

/**Approvals */
Route::middleware('auth:sanctum')->get('/approval/check', [ApprovalController::class, 'check']);
Route::middleware(['auth:sanctum', 'ability:admin,super-admin,marketing'])->post('/approval', [ApprovalController::class, 'user']);