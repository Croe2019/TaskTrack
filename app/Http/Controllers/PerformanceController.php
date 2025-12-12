<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Task;
use App\Models\Tag;
use Carbon\Carbon;
use App\Exports\PerformanceExport;
use Maatwebsite\Excel\Facades\Excel;

class PerformanceController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));
        $tagId = $request->input('tag_id');
        $priority = $request->input('priority');

        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $end   = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        $query = Task::where('user_id', Auth::id())
            ->whereNotNull('completed_at')
            ->whereBetween('completed_at', [$start, $end])
            ->with('tags');

        if ($tagId) {
            $query->whereHas('tags', function ($q) use ($tagId) {
                $q->where('tags.id', $tagId);
            });
        }

        if ($priority) {
            $query->where('priority', $priority);
        }

        $tasks = $query->get();

        // ② フィルターに関係なく 常に全完了タスク を取得（テーブルとエクスポート用）
        $allTasks = Task::where('user_id', Auth::id())
            ->whereNotNull('completed_at')
            ->orderBy('completed_at', 'desc')
            ->get(['title', 'completed_at', 'worked_minutes']);

        // 日付ごとに件数を集計
        $dailyCounts = $allTasks
            ->groupBy(fn($task) => $task->completed_at->format('Y-m-d'))
            ->map(fn($tasks) => $tasks->count());

        // 合計作業時間を追加
        $totalWorkedMinutes = Task::where('user_id', Auth::id())
            ->whereNotNull('completed_at')
            ->sum('worked_minutes');


        return view('performance.index', [
            'tasks'            => $tasks,
            'month'            => $month,
            'allTasks'         => $allTasks,
            'dailyCounts'      => $dailyCounts,
            'totalWorkedMinutes' => $totalWorkedMinutes,
            'tags'             => Tag::all(),
            'selectedTag'      => $tagId,
            'selectedPriority' => $priority,
        ]);
    }



    /**
     * Chart.js用JSON API（必要なら）
     */
    public function chartApi(Request $request)
    {
        $month = $request->input('month', Carbon::now()->format('Y-m'));
        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $end   = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        $userId = Auth::id();

        $tasks = Task::where('user_id', $userId)
            ->whereNotNull('completed_at')
            ->whereBetween('completed_at', [$start, $end])
            ->get();

        $chartData = $tasks->groupBy(function ($task) {
            return Carbon::parse($task->completed_at)->format('d');
        })->map(fn($items) => $items->count());

        return response()->json([
            'labels' => $chartData->keys(),
            'data'   => $chartData->values(),
        ]);
    }

    public function exportCsv(Request $request)
    {
        return Excel::download(
            new PerformanceExport(Auth::id()),
            'performance.csv',
            \Maatwebsite\Excel\Excel::CSV
        );
    }

    public function exportExcel()
    {
        return Excel::download(
            new PerformanceExport(Auth::id()),
            'performance.xlsx',
            \Maatwebsite\Excel\Excel::XLSX
        );
    }

}
