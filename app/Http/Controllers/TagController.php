<?php

namespace App\Http\Controllers;

use App\Services\TagService;
use Illuminate\Http\Request;
use App\Http\Requests\TagRequest;
use App\Http\Requests\UpdateTagRequest;

class TagController extends Controller
{
    private $service;

    public function __construct(TagService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $tags = $this->service->list();
        // viewをどう返すか検討する
        return view('tags.index', compact('tags'));
    }

    public function store(TagRequest $request)
    {
        $this->service->create($request->validated());
        return back()->with('success', 'タグを作成しました');
    }

    public function update(UpdateTagRequest $request, $id)
    {
        $this->service->update($id, $request->validated());
        return back()->with('success', 'タグを更新しました');
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return back()->with('success', 'タグを削除しました');
    }

}
