<?php

use App\Http\Controllers\ThreadController;
use App\Http\Controllers\UserController;
use App\Http\Resources\ThreadResource;
use App\Models\Post;
use App\Models\Thread;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

Route::get('threads', [ThreadController::class, 'index'])->name('threads.index');
Route::get('users', [UserController::class, 'index'])->name('users.index');