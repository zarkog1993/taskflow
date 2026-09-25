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
use App\Http\Controllers\ClubController;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/subscription-plans', [OnboardingController::class, 'plans']);
Route::get('/onboarding/{token}', [OnboardingController::class, 'show']);
Route::post('/onboarding/{token}/select', [OnboardingController::class, 'select']);
Route::get('/trainings/{session}/rsvp/{user}/{status}', [TrainingSessionController::class, 'handleRsvp'])
    ->name('trainings.rsvp')
    ->middleware('signed');

// Zaštićene rute (zahtevaju važeći Bearer token)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/super-admin/dashboard', [UserController::class, 'superAdminDashboard']);
    Route::get('/club', [ClubController::class, 'show'])->middleware('subscription.feature:club.profile');
    Route::put('/club', [ClubController::class, 'update'])->middleware('subscription.feature:club.profile');

    // RESTful User CRUD rute
    Route::apiResource('users', UserController::class)->middleware('subscription.feature:advanced.management');
    // Super Admin pregled svih klubova i vlasnika
    Route::get('/admin/clubs', [UserController::class, 'adminClubsOverview']);

    // RESTful Player CRUD rute
    Route::apiResource('players', PlayerController::class)->middleware('subscription.feature:players');

    Route::get('roles', [RoleController::class, 'index']);

    // Rute za Trening Sesije
    Route::get('/training-sessions', [TrainingSessionController::class, 'index'])->middleware('subscription.feature:teams');
    Route::post('/training-sessions', [TrainingSessionController::class, 'store'])->middleware('subscription.feature:teams');
    Route::put('/training-sessions/{trainingSession}/status', [TrainingSessionController::class, 'updateStatus'])->middleware('subscription.feature:teams');
    Route::put('/training-sessions/{trainingSession}', [TrainingSessionController::class, 'update'])->middleware('subscription.feature:teams');
    Route::delete('/training-sessions/{trainingSession}', [TrainingSessionController::class, 'destroy'])->middleware('subscription.feature:teams');

    // Upravljanje Timovima / Starosnim Grupadama
    Route::get('/teams', [TeamController::class, 'index'])->middleware('subscription.feature:teams');
    Route::post('/teams', [TeamController::class, 'store'])->middleware('subscription.feature:teams');
    Route::get('/teams/{team}', [TeamController::class, 'show'])->middleware('subscription.feature:teams');
    Route::put('/teams/{team}', [TeamController::class, 'update'])->middleware('subscription.feature:teams');
    Route::delete('/teams/{team}', [TeamController::class, 'destroy'])->middleware('subscription.feature:teams');
    Route::post('/teams/{team}/members', [TeamController::class, 'assignMembers'])->middleware('subscription.feature:teams');

    Route::get('/matches', [MatchDayController::class, 'index'])->middleware('subscription.feature:matches');
    Route::post('/matches', [MatchDayController::class, 'store'])->middleware('subscription.feature:matches');
    Route::put('/matches/{match}/status', [MatchDayController::class, 'updateStatus'])->middleware('subscription.feature:matches');
    Route::put('/matches/{match}/stats', [MatchDayController::class, 'updateStats'])->middleware('subscription.feature:advanced.statistics');
    Route::put('/matches/{match}', [MatchDayController::class, 'update'])->middleware('subscription.feature:matches');
    Route::delete('/matches/{match}', [MatchDayController::class, 'destroy'])->middleware('subscription.feature:matches');

    Route::post('/training-sessions/{trainingSession}/attendance', [TrainingSessionController::class, 'syncAttendance'])->middleware('subscription.feature:teams');
    Route::post('/onboarding/complete', [OnboardingController::class, 'store']);
    Route::patch('/subscriptions/{subscription}/status', [OnboardingController::class, 'updateSubscription']);
});
