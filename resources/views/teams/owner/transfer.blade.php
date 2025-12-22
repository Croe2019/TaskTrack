@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto p-6 bg-white rounded shadow">

    <h1 class="text-xl font-bold text-red-600 mb-4">
        Owner 移譲
    </h1>

    <p class="text-sm text-gray-600 mb-6">
        Owner を他のメンバーに移譲します。<br>
        移譲後、あなたは <strong>管理者（Admin）</strong> になります。
    </p>

    {{-- エラーメッセージ --}}
    @if ($errors->any())
        <div class="mb-4 text-red-600 text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST"
          action="{{ route('teams.owner.transfer.store', $team) }}">
        @csrf
        @method('PATCH')

        {{-- 移譲先 --}}
        <div class="mb-6">
            <label class="block text-sm font-medium mb-2">
                新しい Owner
            </label>

            <select name="new_owner_id"
                    class="w-full border rounded px-3 py-2"
                    required>
                <option value="">選択してください</option>

                @foreach ($members as $member)
                    <option value="{{ $member->id }}">
                        {{ $member->name }}
                        （{{ ucfirst($member->pivot->role) }}）
                    </option>
                @endforeach
            </select>

            <p class="text-xs text-gray-500 mt-2">
                Admin / Member のどちらも選択できます
            </p>
        </div>

        {{-- 操作 --}}
        <div class="flex justify-between items-center">
            <a href="{{ route('teams.show', $team) }}"
               class="text-sm text-gray-600 hover:underline">
                キャンセル
            </a>

            <button type="submit"
                    class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                Owner を移譲する
            </button>
        </div>
    </form>

</div>
@endsection
