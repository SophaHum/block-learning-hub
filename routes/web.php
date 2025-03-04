<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\RoleController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('blog', function () {
    return Inertia::render('Blog');
})->name('blog')->middleware(['auth', 'verified']);

Route::middleware(['auth', 'role:admin'])->group(function () {
    // Route::get('/admin', function () {
    //     return 'Admin Dashboard';
    // });
});

Route::middleware(['auth', 'permission:create post'])->group(function () {
    Route::get('/posts/create', [PostController::class, 'create']);
});

//Roles roles.index
Route::middleware(['auth', 'permission:views roles'])->group(function () {
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
});


Route::resource('posts', PostController::class);

Route::resource('categories', CategoryController::class);

Route::resource('landing', LandingController::class);

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
