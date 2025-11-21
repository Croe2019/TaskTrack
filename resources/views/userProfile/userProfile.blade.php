@extends('layouts.app')

@section('content')
<style>

    .container {
        max-width: 600px;
        margin: auto;
        background: #fff;
        padding: 24px;
        border-radius: 12px;
        box-shadow: 0px 4px 12px rgba(0,0,0,0.1);
    }
    h2 {
        margin-bottom: 20px;
        font-size: 22px;
        border-bottom: 2px solid #ddd;
        padding-bottom: 10px;
    }
    .form-group {
        margin-bottom: 18px;
    }
    label {
        display: block;
        font-weight: bold;
        margin-bottom: 6px;
    }
    input[type="text"],
    input[type="email"],
    input[type="password"],
    input[type="file"],
    select,
    textarea {
        width: 100%;
        padding: 10px;
        font-size: 14px;
        border: 1px solid #ccc;
        border-radius: 6px;
        box-sizing: border-box;
    }
    textarea {
        height: 100px;
        resize: vertical;
    }
    .buttons {
        margin-top: 24px;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }
    .btn {
        padding: 10px 20px;
        font-size: 14px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
    }
    .btn-submit {
        background: #4CAF50;
        color: white;
    }
    .btn-cancel {
        background: #ccc;
    }
    .btn-submit:hover { background: #45a049; }
    .btn-cancel:hover { background: #b5b5b5; }

    .alert {
        padding: 10px;
        border-radius: 6px;
        margin-bottom: 10px;
    }
    .alert-success { background: #d4edda; color: #155724; }
    .alert-error { background: #f8d7da; color: #721c24; }

    .avatar-preview {
        width: 80px;
        margin-top: 5px;
        border-radius: 50%;
        object-fit: cover;
    }
</style>

<div class="container">
    <h2>プロフィール編集</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- 名前 -->
        <div class="form-group">
            <label for="name">名前</label>
            <input id="name" type="text" name="name" value="{{ old('name', $profile->name) }}" required>
        </div>

        <!-- メール -->
        <div class="form-group">
            <label for="email">メール</label>
            <input id="email" type="email" name="email" value="{{ old('email', $profile->email) }}" required>
        </div>

        <!-- パスワード -->
        <div class="form-group">
            <label for="password">パスワード（変更する場合のみ）</label>
            <input id="password" type="password" name="password">
            <input id="password_confirmation" type="password" name="password_confirmation" placeholder="確認用">
        </div>

        <!-- プロフィール画像 -->
        <div class="form-group">
            <label for="avatar">プロフィール画像</label>
            <input id="avatar" type="file" name="avatar" accept="image/*" onchange="previewAvatar(event)">
            <img id="avatarPreview" class="avatar-preview"
                src="{{ $profile->avatar ? asset('storage/' . $profile->avatar) : '' }}"
                alt="Avatar">
        </div>

        <!-- 自己紹介 -->
        <div class="form-group">
            <label for="bio">自己紹介</label>
            <textarea id="bio" name="bio">{{ old('bio', $profile->bio) }}</textarea>
        </div>

        <div class="buttons">
            <button type="submit" class="btn btn-submit">保存</button>
            <a href="{{ route('dashboard') }}" class="btn btn-cancel">キャンセル</a>
        </div>
    </form>
</div>

<script>
    function previewAvatar(event) {
        const reader = new FileReader();
        reader.onload = function(){
            const output = document.getElementById('avatarPreview');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
@endsection
