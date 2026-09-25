<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Catch-all ruta za Vue SPA Router
// routes/web.php
Route::get('/{any}', function () {
    return view('welcome'); // ili 'app' ako stvoriš resources/views/app.blade.php
})->where('any', '.*');