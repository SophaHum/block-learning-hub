<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('blog', function () {
    return Inertia::render('Blog');
})->name('blog')->middleware(['auth', 'verified']);

Route::middleware(['auth'])->group(function () {
    // Role-based routes
    Route::middleware(['role:admin'])->group(function () {
        // Admin only routes
    });
    Route::get('/posts/create', [PostController::class, 'create']);

    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');

    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::get('/permissions/{id}', [PermissionController::class, 'show'])->name('permissions.show');

    Route::get('/permissions/create', [PermissionController::class, 'create'])->name('permissions.create');
    Route::post('/permissions', [PermissionController::class, 'store'])->name('permissions.store');
    // User Management Routes with permission middleware
    Route::resource('users', UserController::class);
});

Route::resource('posts', PostController::class);
Route::resource('categories', CategoryController::class);
Route::resource('landing', LandingController::class);

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
