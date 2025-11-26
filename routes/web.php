<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\TaskController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\CommentController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/dashboard', [DashboardController::class, 'dashboard'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::get('tasks', [TaskController::class, 'index'])
    ->middleware(['auth'])
    ->name('tasks.index');

Route::get('tasks/create', [TaskController::class, 'create'])
    ->middleware(['auth'])
    ->name('tasks.create');

Route::post('tasks/store', [TaskController::class, 'store'])
    ->middleware(['auth'])
    ->name('tasks.store');

Route::get('tasks/show/{id}', [TaskController::class, 'show'])
    ->middleware(['auth'])
    ->name('tasks.show');

Route::put('tasks/{task}', [TaskController::class, 'update'])
    ->middleware(['auth'])
    ->name('tasks.update');

Route::delete('tasks/destroy/{id}', [TaskController::class, 'destroy'])
    ->middleware(['auth'])
    ->name('tasks.destroy');

Route::patch('tasks/{task}/complete', [TaskController::class, 'complete'])
    ->middleware(['auth'])
    ->name('tasks.complete');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile/edit', [UserProfileController::class, 'edit'])->name('userProfile.userProfile');
    // 後で実装する
    Route::put('/profile', [UserProfileController::class, 'update'])->name('profile.update');

    Route::post('/tasks/{task}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    Route::put('/comments/{commentId}', [CommentController::class, 'update'])->name('comments.update');

});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
