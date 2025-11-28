<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\TaskService;
use App\Repositories\TagRepository;
use App\Http\Requests\Task\CreateTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    protected TaskService $service;
    protected TagRepository $tagRepo;

    public function __construct(TaskService $service, TagRepository $tagRepo)
    {
        $this->service = $service;
        $this->tagRepo = $tagRepo;
    }

    public function index(Request $request)
    {
        // 必要ならリクエストフィルタを渡す
        $tasks = $this->service->getAllTasks();
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

        // 既存タグ
        $existingTagIds = $data['tag_ids'] ?? [];

        // 新規タグ（文字列 → 配列）
        $newTagNames = [];
        if (!empty($data['tags'])) {
            $newTagNames = array_map('trim', explode(',', $data['tags']));
        }

        // サービス呼び出し
        $this->service->createTaskWithTags($data, $newTagNames, $existingTagIds);

        return redirect()->route('tasks.index')
            ->with('success', 'タスクを作成しました');
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
        $task = $this->service->getFindTask($id);
        return view('tasks.show', compact('task'));
    }

    // 更新
    public function update(CreateTaskRequest $request, $id)
    {
        $task = $this->service->getFindTask($id);
        if ($task->user_id !== Auth::id()) {
            return redirect()->back()->with('error', '他ユーザーのタスクは編集できません');
        }

        try {
            $this->service->updateTask($id, $request->validated());
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

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
}
