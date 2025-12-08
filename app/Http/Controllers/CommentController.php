<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Models\Comment;
use App\Models\Task;
use App\Services\CommentService;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    private $service;

    public function __construct(CommentService $service)
    {
        $this->service = $service;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = Task::with(['comments.user'])->get();

        return view('tasks.index', compact('tasks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCommentRequest $request, $taskId)
    {
        $data = $request->validated();
        $this->service->add($taskId, $data['content']);
        return back()->with('success', 'コメントを追加しました');
    }

    /**
     * Display the specified resource.
     */
    public function show(Comment $comment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Comment $comment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
   public function update(UpdateCommentRequest $request, $id)
    {
            $updatedIds = $this->service->update($id, [
            'content' => $request->content
        ]);

        return back()->with('updated_ids', $updatedIds);
    }

    public function destroy($id)
    {
        $deletedIds = $this->service->delete($id);

        return back()->with('deleted_ids', $deletedIds);
    }

}
