@extends('layouts.app')

@section('content')
<div class="container" style="max-width:760px; margin:auto; padding:20px;">
    <h2>チーム作成</h2>

    <form method="POST" action="{{ route('teams.store') }}" enctype="multipart/form-data">
        @csrf
        <div style="margin-bottom:12px;">
            <label>チーム名</label><br>
            <input name="name" value="{{ old('name') }}" required style="width:100%; padding:8px;">
        </div>

        <div style="margin-bottom:12px;">
            <label>説明</label><br>
            <textarea name="description" style="width:100%; padding:8px;">{{ old('description') }}</textarea>
        </div>

        <div style="text-align:right;">
            <button type="submit" style="padding:8px 16px; background:#1d4ed8; color:#fff; border:none; border-radius:6px;">作成</button>
        </div>
    </form>
</div>
@endsection
