@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6">

    <h1 class="text-2xl font-bold mb-6">チーム設定</h1>

    {{-- チーム基本情報 --}}
    <div class="bg-white p-6 rounded-xl shadow mb-6">
        <h2 class="text-lg font-semibold mb-4">基本情報</h2>

        <form method="POST" action="{{ route('teams.update', $team) }}">
            @csrf
            @method('PUT')

            {{-- チーム名 --}}
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">チーム名</label>
                <input
                    type="text"
                    name="name"
                    value="{{ old('name', $team->name) }}"
                    class="w-full border rounded px-3 py-2"
                    required
                >
            </div>

            {{-- 説明 --}}
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">説明</label>
                <textarea
                    name="description"
                    rows="3"
                    class="w-full border rounded px-3 py-2"
                >{{ old('description', $team->description) }}</textarea>
            </div>

            <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                保存
            </button>

            <a href="{{ route('teams.show', $team) }}"
            class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                キャンセル
            </a>
        </form>
    </div>

    {{-- メンバー管理 --}}
    <div class="bg-white p-6 rounded-xl shadow">
        <h2 class="text-lg font-semibold mb-4">メンバー管理</h2>

        <table class="w-full border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2 text-left">ユーザー</th>
                    <th class="p-2">ロール</th>
                    <th class="p-2">操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($team->members as $member)
                    <tr class="border-t">
                        <td class="p-2">
                            {{ $member->name }}
                            @if ($member->pivot->role === 'owner')
                                <span class="ml-2 text-xs bg-yellow-200 px-2 py-0.5 rounded">
                                    Owner
                                </span>
                            @endif
                        </td>

                        <td class="p-2 text-center">
                            {{ ucfirst($member->pivot->role) }}
                        </td>

                        <td class="p-2 text-center space-x-2">

                            {{-- オーナー移譲 --}}
                            @if (
                                auth()->id() === $team->owner_id &&
                                $member->id !== auth()->id()
                            )
                                <form method="POST"
                                      action="{{ route('teams.transfer-owner', [$team, $member]) }}"
                                      class="inline">
                                    @csrf
                                    <button
                                        class="px-2 py-1 text-sm bg-yellow-500 text-white rounded"
                                        onclick="return confirm('オーナーを移譲しますか？')"
                                    >
                                        オーナー移譲
                                    </button>
                                </form>
                            @endif

                            {{-- Admin 昇格 / 降格 --}}
                            @if ($member->pivot->role === 'member')
                                <form method="POST"
                                      action="{{ route('teams.members.updateRole', [$team, $member]) }}"
                                      class="inline">
                                    @csrf
                                    @method('PATCH')
                                     <input type="hidden" name="role" value="admin">
                                    <button class="px-2 py-1 text-sm bg-green-600 text-white rounded">
                                        Adminに昇格
                                    </button>
                                </form>
                            @elseif ($member->pivot->role === 'admin')
                                <form method="POST"
                                      action="{{ route('teams.members.updateRole', [$team, $member]) }}"
                                      class="inline">
                                    @csrf
                                    @method('PATCH')
                                     <input type="hidden" name="role" value="member">
                                    <button class="px-2 py-1 text-sm bg-gray-500 text-white rounded">
                                        Admin解除
                                    </button>
                                </form>
                            @endif

                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p class="text-sm text-gray-500 mt-4">
            ※ オーナーが0人になる操作はサーバー側で禁止されています
        </p>
    </div>

</div>
@endsection
