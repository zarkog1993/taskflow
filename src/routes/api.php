<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RoleController;
use Illuminate\Http\Request;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TrainingSessionController;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Zaštićene rute (zahtevaju važeći Bearer token)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // RESTful User CRUD rute
    Route::apiResource('users', UserController::class);
    Route::apiResource('tasks', TaskController::class);
    Route::get('/tasks/{task}/comments', [CommentController::class, 'index']);
    Route::post('/tasks/{task}/comments', [CommentController::class, 'store']);

    // Direktne rute za komentare
    Route::put('/comments/{comment}', [CommentController::class, 'update']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread', [NotificationController::class, 'unread']);
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);

    // Rute za upravljanje korisnicima i ulogama
    Route::apiResource('users', UserController::class);
    Route::put('users/{user}/roles', [UserController::class, 'updateRoles']);
    Route::put('/users/{user}/stats', [UserController::class, 'updateStats']);
    Route::put('/users/{user}/profile', [UserController::class, 'updateProfile']);
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);

    Route::get('roles', [RoleController::class, 'index']);

    // Rute za Trening Sesije
    Route::get('/training-sessions', [TrainingSessionController::class, 'index']);
    Route::post('/training-sessions', [TrainingSessionController::class, 'store']);
    Route::put('/training-sessions/{trainingSession}/status', [TrainingSessionController::class, 'updateStatus']);

    // Upravljanje Timovima / Starosnim Grupadama
    Route::get('/teams', [TeamController::class, 'index']);
    Route::post('/teams', [TeamController::class, 'store']);
    Route::post('/teams/{team}/members', [TeamController::class, 'assignMembers']);

    Route::post('/training-sessions/{trainingSession}/attendance', [TrainingSessionController::class, 'syncAttendance']);
});
