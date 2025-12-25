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
        <nav class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-3">

            <div class="flex items-center justify-between">
                <!-- Left: Logo -->
                <div class="text-xl font-bold">
                    <a href="{{ route('dashboard') }}">TaskTrack</a>
                </div>

                <!-- Mobile toggle button -->
                <button id="mobile-menu-toggle" type="button" class="md:hidden inline-flex items-center justify-center p-2 text-gray-600 hover:text-gray-900 focus:outline-none" aria-controls="mobile-menu" aria-expanded="false">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <!-- Right: Navigation (desktop) -->
                <ul class="hidden md:flex gap-6 text-gray-700 font-medium">
                    <li><a href="{{ route('tasks.index') }}" class="hover:text-blue-600">タスク一覧</a></li>
                    <li><a href="{{ route('performance.index') }}" class="hover:text-blue-600">実績</a></li>
                    <li><a href="{{ route('teams.index') }}" class="hover:text-blue-600">チーム</a></li>
                    <li><a href="{{ route('userProfile.userProfile') }}" class="hover:text-blue-600">プロフィール</a></li>

                    <!-- Logout -->
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="hover:text-red-600">ログアウト</button>
                        </form>
                    </li>
                </ul>
            </div>

            <!-- Mobile Navigation -->
            <ul id="mobile-menu" class="hidden flex-col gap-3 mt-3 text-gray-700 font-medium md:hidden">
                <li><a href="{{ route('tasks.index') }}" class="block w-full py-1 hover:text-blue-600">タスク一覧</a></li>
                <li><a href="{{ route('performance.index') }}" class="block w-full py-1 hover:text-blue-600">実績</a></li>
                <li><a href="{{ route('teams.index') }}" class="block w-full py-1 hover:text-blue-600">チーム</a></li>
                <li><a href="{{ route('userProfile.userProfile') }}" class="block w-full py-1 hover:text-blue-600">プロフィール</a></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button class="w-full text-left py-1 hover:text-red-600">ログアウト</button>
                    </form>
                </li>
            </ul>

        </nav>
    </header>

    <!-- メインコンテンツ -->
    <main class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @yield('content')

    </main>
    @include('layouts.delete-modal')


    <!-- フッター -->
    <footer class="text-center py-4 text-gray-600 text-sm">
        © TaskTrack 2025
    </footer>

    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggleButton = document.getElementById('mobile-menu-toggle');
            const mobileMenu = document.getElementById('mobile-menu');

            if (!toggleButton || !mobileMenu) return;

            toggleButton.addEventListener('click', () => {
                const isHidden = mobileMenu.classList.toggle('hidden');
                toggleButton.setAttribute('aria-expanded', (!isHidden).toString());
            });
        });
    </script>

</body>
</html>
