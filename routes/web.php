<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TaskTagController;
use App\Http\Controllers\PerformanceController;

Route::get('/', function () {
    return view('welcome');
});



Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::get('tasks/create', [TaskController::class, 'create'])->name('tasks.create');
    Route::post('tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::get('tasks/{id}', [TaskController::class, 'show'])->name('tasks.show');
    Route::get('tasks/{id}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
    Route::put('tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('tasks/{id}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::patch('tasks/{id}/complete', [TaskController::class, 'complete'])->name('tasks.complete');

    Route::post('/tasks/{task}/start', [TaskController::class, 'start'])->name('tasks.start');
    Route::post('/tasks/{task}/stop', [TaskController::class, 'stop'])->name('tasks.stop');

    Route::get('/profile/edit', [UserProfileController::class, 'edit'])->name('userProfile.userProfile');
    // 後で実装する
    Route::put('/profile', [UserProfileController::class, 'update'])->name('profile.update');

    Route::post('/tasks/{task}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    Route::put('/comments/{commentId}', [CommentController::class, 'update'])->name('comments.update');

    // タグ管理
    Route::resource('tags', TagController::class)->except(['create', 'edit', 'show']);

    // タグ付け
    Route::put('/tasks/{id}/tags', [TaskTagController::class, 'update'])->name('tasks.tags.update');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/performance', [PerformanceController::class, 'index'])->name('performance.index');
    Route::get('/performance/export/csv', [PerformanceController::class, 'exportCsv'])->name('performance.export.csv');
    Route::get('/performance/export/excel', [PerformanceController::class, 'exportExcel'])->name('performance.export.excel');


});

require __DIR__.'/auth.php';
