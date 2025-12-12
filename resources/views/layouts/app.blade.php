<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskTrack</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100">

    <!-- ヘッダー（ナビバー） -->
    <header class="bg-white shadow">
        <nav class="max-w-7xl mx-auto flex items-center justify-between px-4 py-3">

            <!-- Left: Logo -->
            <div class="text-xl font-bold">
                <a href="{{ route('dashboard') }}">TaskTrack</a>
            </div>

            <!-- Right: Navigation -->
            <ul class="flex gap-6 text-gray-700 font-medium">

                <li><a href="{{ route('tasks.index') }}" class="hover:text-blue-600">タスク一覧</a></li>
                <li><a href="{{ route('performance.index') }}" class="hover:text-blue-600">実績</a></li>
                <li><a href="" class="hover:text-blue-600">チーム</a></li>
                <li><a href="{{ route('userProfile.userProfile') }}" class="hover:text-blue-600">プロフィール</a></li>

                <!-- Logout -->
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="hover:text-red-600">ログアウト</button>
                    </form>
                </li>
            </ul>

        </nav>
    </header>

    <!-- メインコンテンツ -->
    <main class="max-w-7xl mx-auto px-4 py-6">
        @yield('content')
    </main>

    <!-- フッター -->
    <footer class="text-center py-4 text-gray-600 text-sm">
        © TaskTrack 2025
    </footer>

</body>
</html>
