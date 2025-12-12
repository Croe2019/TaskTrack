<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\TaskService;
use App\Repositories\TagRepository;
use App\Http\Requests\Task\CreateTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Task;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class TaskController extends Controller
{
    use AuthorizesRequests;
    protected TaskService $service;
    protected TagRepository $tagRepo;

    public function __construct(TaskService $service, TagRepository $tagRepo)
    {
        $this->service = $service;
        $this->tagRepo = $tagRepo;
    }

    public function index(Request $request)
    {
        // 基本のクエリ：ログインユーザーのタスクを取得 TODO ここから再開
        $query = Task::with(['attachments', 'tags', 'comments.user'])->where('user_id', Auth::id());

        // ステータスフィルター
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // キーワード検索（タイトルまたはタグ名）
        if ($keyword = $request->input('keyword')) {
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                ->orWhereHas('tags', function ($q2) use ($keyword) {
                    $q2->where('name', 'like', "%{$keyword}%");
                });
            });
        }

        // クエリ実行
        $tasks = $query->get();

        return view('tasks.index', compact('tasks'));
    }


    public function create()
    {
        $tags = $this->tagRepo->getAll();
        return view('tasks.create', compact('tags'));
    }

    // 保存
    public function store(CreateTaskRequest $request)
    {
        $data = $request->validated();

        $existingTagIds = $data['tag_ids'] ?? [];
        $newTagNames = !empty($data['tags']) ? array_map('trim', explode(',', $data['tags'])) : [];
        $files = $request->file('attachments', []);

        $this->service->createTaskWithTagsAndAttachments($data, $existingTagIds, $newTagNames, $files);

        return redirect()->route('tasks.index')->with('success', 'タスクを作成しました');
    }


    /**
     * 編集フォーム
     */
    public function edit($id)
    {
        $task = $this->service->getFindTask($id);
        // 権限チェック：作成者のみ編集可（コントローラでも明示的に）
        if ($task->user_id !== Auth::id()) {
            return redirect()->route('tasks.index')->with('error', '他ユーザーのタスクは編集できません');
        }

        $tags = $this->tagRepo->getAll();
        return view('tasks.edit', compact('task', 'tags'));
    }

    /**
     * 表示(単体)
     */
    public function show($id)
    {
        $task = Task::with(['attachments', 'tags'])->findOrFail($id);
        if ($task->work_start_at) {
                // 作業中なら現在時刻まで加算した値を追加
                $task->current_minutes =
                    $task->worked_minutes +
                    $task->work_start_at->diffInMinutes(now());
            } else {
                $task->current_minutes = $task->worked_minutes;
            }
        return view('tasks.show', compact('task'));
    }

    // 更新
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();


        $existingTagIds = $data['tag_ids'] ?? [];
        $newTagNames = !empty($data['tags']) ? array_map('trim', explode(',', $data['tags'])) : [];
        $files = $request->file('attachments', []);


        $this->service->updateTaskWithTagsAndAttachments(
            $task, $data, $existingTagIds, $newTagNames, $files
        );

        return redirect()->route('tasks.index')->with('success', 'タスクを更新しました');
    }

    // 削除
    public function destroy($id)
    {
        $task = $this->service->getFindTask($id);
        if ($task->user_id !== Auth::id()) {
            return redirect()->back()->with('error', '他ユーザーのタスクは削除できません');
        }

        $this->service->deleteTask($id);
        return redirect()->route('tasks.index')->with('success', 'タスクを削除しました');
    }


    /**
     * 完了（PATCH）
     */
    public function complete($id)
    {
        $task = $this->service->getFindTask($id);
        if ($task->user_id !== Auth::id()) {
            return redirect()->back()->with('error', '他ユーザーのタスクは完了できません');
        }

        $this->service->completeTask($id);
        return redirect()->route('tasks.index')->with('success', 'タスクを完了しました');
    }

    public function start(Task $task)
    {
        $this->authorize('update', $task);

        if ($task->work_start_at) {
            return back()->with('error', 'すでに作業中です。');
        }

        $task->update([
            'work_start_at' => now(),
        ]);

        return back()->with('success', '作業を開始しました。');
    }


    public function stop(Task $task)
    {
        $this->authorize('update', $task);

        if (!$task->work_start_at) {
            return back()->with('error', '作業は開始されていません。');
        }

        $minutes = $task->work_start_at->diffInMinutes(now());

        $task->update([
            'worked_minutes' => $task->worked_minutes + $minutes,
            'work_start_at' => null,
        ]);

        return back()->with('success', '作業を停止しました。');
    }

}
