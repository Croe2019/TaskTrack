<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Tag;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $userId = Auth::id();

        // 今月（yyyy-mm）
        $currentMonth = now()->format('Y-m');

        // 今月の総タスク数
        $monthlyTask = Task::where('user_id', $userId)
            ->whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$currentMonth])
            ->count();

        // 今月の完了タスク
        $monthlyCompleted = Task::where('user_id', $userId)
            ->whereNotNull('completed_at')
            ->whereRaw('DATE_FORMAT(completed_at, "%Y-%m") = ?', [$currentMonth])
            ->count();

        // 完了率
        $monthlyRate = $monthlyTask > 0
            ? round(($monthlyCompleted / $monthlyTask) * 100, 1)
            : 0;

        // 月別完了タスク数（グラフ用）
        $monthlyCounts = Task::selectRaw('DATE_FORMAT(completed_at, "%Y-%m") as month, COUNT(*) as count')
            ->where('user_id', $userId)
            ->whereNotNull('completed_at')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // 最近完了タスク
        $allTasks = Task::where('user_id', $userId)
            ->whereNotNull('completed_at')
            ->orderBy('completed_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'monthlyTask',
            'monthlyCompleted',
            'monthlyRate',
            'monthlyCounts',
            'allTasks',
        ));
    }

}
