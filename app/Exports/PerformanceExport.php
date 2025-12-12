<?php

namespace App\Exports;

use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class PerformanceExport implements FromCollection, WithHeadings, WithMapping, WithCustomCsvSettings
{
    public function collection()
    {
        return Task::where('user_id', Auth::id())
            ->whereNotNull('completed_at')
            ->get(['title', 'completed_at', 'worked_minutes']);
    }

    public function map($task): array
    {
        return [
            $task->title,
            optional($task->completed_at)->format('Y-m-d H:i'),
            $task->worked_minutes,
        ];
    }

    public function headings(): array
    {
        return ['タイトル', '完了日時', '作業時間（分）'];
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ',',
            'enclosure' => '"',
            'line_ending' => "\n",
            'use_bom' => true,
        ];
    }
}
