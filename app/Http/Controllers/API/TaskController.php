<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\TaskService;
use App\Http\Requests\Task\CreateTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;


class TaskController extends Controller
{
    protected $service;

    public function __construct(TaskService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $tasks = $this->service->getAllTasks();
        return view('tasks.index', compact('tasks'));
    }

    public function create()
    {
        return view('tasks.create');
    }

    public function store(CreateTaskRequest $request)
    {
        $this->service->createTask($request->validated());
        return redirect()->route('tasks.index')->with('success', 'タスクを作成しました');
    }

    public function show($id)
    {
        $task = $this->service->getFindTask($id);
        return view('tasks.show', compact('task'));
    }

    public function update(UpdateTaskRequest $request, $id)
    {
        try{
            $this->service->updateTask($id, $request->validated());
            return redirect()->route('tasks.index')->with('success', 'タスクを更新しました');
        }catch(\Exception $e){
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $task = $this->service->getFindTask($id);
        $this->service->deleteTask($task->id);
        return redirect()->route('tasks.index')->with('success', 'タスクを削除しました');
    }

    public function complete($id)
    {
        $this->service->completeTask($id);
        return redirect()->back()->with('success', 'タスクを完了にしました');
    }
}
