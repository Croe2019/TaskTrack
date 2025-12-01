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
        $comment = $this->service->getFindComment($id);

        if ($comment->user_id !== Auth::id()) {
            return redirect()->back()->with('error', '他ユーザーのコメントは編集できません');
        }

        $this->service->update($id, $request->validated());
        return back()->with('success', 'コメントを更新しました');
    }

    public function destroy($id)
    {
        $commentId = $this->service->getFindComment($id);
        $comment = Comment::findOrFail($commentId);

        if ($comment->user_id !== Auth::id()) {
            return redirect()->back()->with('error', '他ユーザーのコメントは削除できません');
        }

        $this->service->remove($id);
        return back()->with('success', 'コメントを削除しました');
    }

}
