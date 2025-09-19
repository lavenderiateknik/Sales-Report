<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Inertia::render('ApiLanding'); // ganti Welcome jadi ApiLanding
});

Route::get('/login', function () {
    return response()->json([
        'message' => 'Silakan login melalui API endpoint /api/login'
    ]);
})->name('login');
