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
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TeamMemberController;
use App\Http\Controllers\TeamInvitationController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\TeamOwnerController;
use App\Http\Controllers\TeamTaskController;
use App\Http\Controllers\TeamTaskCommentController;
use App\Http\Controllers\TeamTaskWorkLogController;


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

    // チーム関連
    Route::get('/teams', [TeamController::class, 'index'])->name('teams.index');
    Route::get('/create', [TeamController::class, 'create'])->name('teams.create');
    Route::post('/store', [TeamController::class, 'store'])->name('teams.store');
    Route::get('/show/{team}', [TeamController::class, 'show'])->name('teams.show');
    Route::get('/edit/{team}', [TeamController::class, 'edit'])->name('teams.edit');
    Route::put('/update/{team}', [TeamController::class, 'update'])->name('teams.update');
    Route::delete('/delete/{team}', [TeamController::class, 'destroy'])->name('teams.destroy');

    Route::get('teams/{team}/members', [TeamMemberController::class, 'index'])->name('teams.members.index');
    Route::post('teams/{team}/members', [TeamMemberController::class, 'addMember'])->name('teams.members.addMember');
    Route::patch('teams/{team}/members/{user}', [TeamMemberController::class, 'updateRole'])->name('teams.members.updateRole');
    Route::delete('teams/{team}/members/{user}', [TeamMemberController::class, 'removeMember'])->name('teams.members.remove');

    Route::get('/teams/invites/index',
        [TeamInvitationController::class, 'index']
    )->name('invites.index');

     // 招待入力画面（GET）
    Route::get('/teams/{team}/invites/create',
        [TeamInvitationController::class, 'create']
    )->name('teams.invites.create');

    // 招待リンク
    Route::post('/teams/{team}/invites', [TeamInvitationController::class, 'store'])->name('teams.invites.store');

    Route::patch('/invites/{invite}/accept', [TeamInvitationController::class, 'accept'])->name('invites.accept');

    Route::patch('/invites/{invite}/reject', [TeamInvitationController::class, 'reject'])->name('invites.reject');


    Route::get('teams/{team}/owner/transfer',[TeamOwnerController::class, 'create'])->name('teams.owner.transfer.create');

    Route::patch('teams/{team}/owner/transfer',[TeamOwnerController::class, 'store'])->name('teams.owner.transfer.store');


    Route::post('teams/switch', function(\Illuminate\Http\Request $r){
        $teamId = $r->input('team_id');
        if ($teamId) {
            $team = \App\Models\Team::find($teamId);
            if ($team && $team->members()->where('user_id', Auth::id())->exists()) {
                session(['current_team_id' => $teamId]);
            } else {
                session()->forget('current_team_id');
            }
        } else {
            session()->forget('current_team_id');
        }
        return back();
    })->name('teams.switch')->middleware('auth');


    Route::prefix('teams/{team}')
        ->name('teams.')
        ->group(function () {

            // チーム単位のダッシュボード（task不要）
            Route::get('tasks/dashboard', [TeamTaskController::class, 'dashboard'])
                ->name('tasks.dashboard');

            // タスク（resource）
            Route::resource('tasks', TeamTaskController::class)
                ->parameters(['tasks' => 'task']);

            Route::patch('tasks/{task}/status', [TeamTaskController::class, 'updateStatus'])
                ->name('tasks.updateStatus');

            // コメント（task配下）
            Route::prefix('tasks/{task}')
                ->name('tasks.')
                ->group(function () {
                    Route::post('comments', [TeamTaskCommentController::class, 'store'])
                        ->name('comments.store');
                    Route::delete('comments/{comment}', [TeamTaskCommentController::class, 'destroy'])
                        ->name('comments.destroy');
                    Route::patch('comments/{comment}', [TeamTaskCommentController::class, 'update'])
                        ->name('comments.update');

                    // work logs
                    Route::post('work-logs', [TeamTaskWorkLogController::class, 'store'])->name('workLogs.store');
                    Route::patch('work-logs/{workLog}', [TeamTaskWorkLogController::class, 'update'])->name('workLogs.update');
                    Route::delete('work-logs/{workLog}', [TeamTaskWorkLogController::class, 'destroy'])->name('workLogs.destroy');
                });
        });



});

require __DIR__.'/auth.php';
