@extends('layouts.app')

@section('content')
<div class="container" style="max-width:600px; margin:auto; background:white; padding:24px; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.1);">
    <body>

        <form class="container" method="POST" action="{{ route('tasks.store') }}" enctype="multipart/form-data">
            <!-- Laravelの場合は追加 -->
            @csrf

            <h2 style="margin-bottom:20px; font-size:22px; border-bottom:2px solid #ddd; padding-bottom:10px;">
                タスク登録
            </h2>

            <!-- タイトル -->
            <div class="form-group" style="margin-bottom:18px;">
            <label style="display:block; font-weight:bold; margin-bottom:6px;">タイトル</label>
                <input type="text" name="title" placeholder="タイトルを入力" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;">
            </div>

            <!-- 詳細内容 -->
            <div class="form-group" style="margin-bottom:18px;">
                <label style="display:block; font-weight:bold; margin-bottom:6px;">詳細内容</label>
                <textarea name="description" placeholder="詳細を入力" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px; height:80px; resize:vertical;"></textarea>
            </div>

            <!-- ステータス -->
            <div class="form-group" style="margin-bottom:18px;">
                <label style="display:block; font-weight:bold; margin-bottom:6px;">ステータス</label>
                <select name="status" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;">
                    <option value="not_started">未着手</option>
                    <option value="in_progress">進行中</option>
                    <option value="completed">完了</option>
                </select>
            </div>

            <!-- 優先度 -->
            <div class="form-group" style="margin-bottom:18px;">
                <label style="display:block; font-weight:bold; margin-bottom:6px;">優先度</label>
                <select name="priority" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;">
                    <option value="high">高</option>
                    <option value="medium">中</option>
                    <option value="low">低</option>
                </select>
            </div>

            <!-- 期限 -->
            <div class="form-group" style="margin-bottom:18px;">
                <label style="display:block; font-weight:bold; margin-bottom:6px;">期限</label>
                <input type="date" name="deadline" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;">
            </div>

            <!-- 完了日時（任意） -->
            <div class="form-group" style="margin-bottom:18px;">
                <label style="display:block; font-weight:bold; margin-bottom:6px;">完了日時</label>
                <input type="datetime-local" name="completed_at" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;">
            </div>

            <h3>新しいタグ（カンマ区切り）</h3>
            <input type="text" name="tags" placeholder="例: 重要, クライアント, バグ">

            <label>添付ファイル</label>
            <input type="file" name="attachments[]" multiple style="margin-bottom:10px;">

            <!-- ボタン -->
            <div class="buttons" style="margin-top:24px; display:flex; justify-content:flex-end; gap:12px;">
                <button class="btn btn-submit" type="submit" style="padding:10px 20px; border:none; border-radius:6px; background:#4CAF50; color:white; cursor:pointer;">登録</button>
                <button class="btn btn-cancel" type="button" onclick="history.back()" style="padding:10px 20px; border:none; border-radius:6px; background:#ccc; cursor:pointer;">キャンセル</button>
            </div>
        </form>
    </body>
</div>
@endsection
