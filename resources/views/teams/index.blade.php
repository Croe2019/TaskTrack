@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6">

    {{-- <h1 class="text-2xl font-bold mb-6">チーム</h1> --}}

    {{-- チーム未所属 --}}
    @if ($teams->isEmpty())
        {{-- ページタイトル --}}
        <h1 class="text-2xl font-bold mb-6">
            チーム
        </h1>

        {{-- アクションエリア（ここだけ中央寄せ） --}}
        <div class="flex justify-center mb-8">
            <a href="{{ route('teams.create') }}"
            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                チームを作成
            </a>
        </div>

        {{-- コンテンツエリア --}}
        <div class="max-w-3xl mx-auto">
            @forelse ($teams as $team)
                <div class="p-4 border rounded mb-4">

                    <h3 class="text-lg font-semibold">
                        {{ $team->name }}
                    </h3>
                </div>
            @empty
                <div class="text-center text-gray-500 mt-12">
                    まだチームがありません
                </div>

                @if ($hasInvites)
                    <div class="text-center mt-6">
                        <a href="{{ route('invites.index') }}"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            チームへの招待があります
                        </a>
                    </div>
                @endif
            @endforelse
        </div>

    @else
        {{-- チーム所属済み --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach ($teams as $team)
                <div class="bg-white p-5 rounded-xl shadow">

                    <h3 class="text-lg font-bold mb-1">
                        {{ $team->name }}
                    </h3>

                    <p class="text-sm text-gray-600 mb-2">
                        {{ $team->description }}
                    </p>

                    <p class="text-sm text-gray-500 mb-3">
                        メンバー数：{{ $team->members_count }}人
                    </p>

                    <a href="{{ route('teams.show', $team) }}"
                       class="inline-block text-blue-600 hover:underline">
                        チームを見る →
                    </a>

                    <div class="mt-3 flex gap-3">
                        @can('createInvite', $team)
                            <a href="{{ route('teams.invites.create', $team) }}"
                            class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
                                メンバー招待
                            </a>
                        @endcan
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection
