<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\RoleMiddlewer;

Route::get('/', function () {
    return Inertia::render('welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::prefix('admin')->name('admin.')->middleware(['auth','role:admin'])->group(function(){
    Route::resource('categories',CategoryController::class)->except(['show']);
    Route::resource('posts',PostController::class)->except(['show']);
});

Route::prefix('user')->name('user.')->middleware(['auth','role:user'])->group(function(){
    Route::get('posts', [UserController::class, 'index'])->name('posts.index');
    Route::get('posts/{id}', [UserController::class, 'show'])->name('posts.show');
    Route::post('posts/{id}/comments', [UserController::class, 'storeComment'])->name('posts.comments.store');
    Route::delete('posts/{postId}/comments/{commentId}', [UserController::class, 'destroyComment'])->name('posts.comments.destroy');
});
require __DIR__.'/settings.php';

