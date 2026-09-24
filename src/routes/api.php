<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MatchDayController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use Illuminate\Http\Request;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TrainingSessionController;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/trainings/{session}/rsvp/{user}/{status}', [TrainingSessionController::class, 'handleRsvp'])
    ->name('trainings.rsvp')
    ->middleware('signed');

// Zaštićene rute (zahtevaju važeći Bearer token)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // RESTful User CRUD rute
    Route::apiResource('users', UserController::class);
    // Super Admin pregled svih klubova i vlasnika
    Route::get('/admin/clubs', [UserController::class, 'adminClubsOverview']);

    // RESTful Player CRUD rute
    Route::apiResource('players', PlayerController::class);

    // Rute za upravljanje korisnicima i ulogama
    Route::apiResource('users', UserController::class);

    Route::get('roles', [RoleController::class, 'index']);

    // Rute za Trening Sesije
    Route::get('/training-sessions', [TrainingSessionController::class, 'index']);
    Route::post('/training-sessions', [TrainingSessionController::class, 'store']);
    Route::put('/training-sessions/{trainingSession}/status', [TrainingSessionController::class, 'updateStatus']);
    Route::delete('/training-sessions/{trainingSession}', [TrainingSessionController::class, 'destroy']);

    // Upravljanje Timovima / Starosnim Grupadama
    Route::get('/teams', [TeamController::class, 'index']);
    Route::post('/teams', [TeamController::class, 'store']);
    Route::post('/teams/{team}/members', [TeamController::class, 'assignMembers']);

    Route::get('/matches', [MatchDayController::class, 'index']);
    Route::post('/matches', [MatchDayController::class, 'store']);
    Route::put('/matches/{match}/status', [MatchDayController::class, 'updateStatus']);
    Route::put('/matches/{match}/stats', [MatchDayController::class, 'updateStats']);
    Route::delete('/matches/{match}', [MatchDayController::class, 'destroy']);

    Route::post('/training-sessions/{trainingSession}/attendance', [TrainingSessionController::class, 'syncAttendance']);
    Route::post('/onboarding/complete', [OnboardingController::class, 'store']);
});
