<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Catch-all ruta za Vue SPA Router
Route::get('/{any}', function () {
    return view('app'); // naziv tvog glavnog blade fajla (resources/views/app.blade.php)
})->where('any', '.*');
