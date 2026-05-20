<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Панель статистики')</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    @stack('scripts')
</head>
<body class="bg-gray-100 font-sans @yield('body_class')">

<!-- Шапка (Показывается только авторизованным пользователям) -->
@auth
    <nav class="bg-white shadow mb-8">
        <div class="max-w-6xl mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-xl font-bold text-gray-800">Счетчик посещений (Статистика)</h1>
            <div class="flex items-center gap-4">
                <span class="text-gray-600 text-sm">{{ Auth::user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-semibold">Выйти</button>
                </form>
            </div>
        </div>
    </nav>
@endauth

<!-- Основной контент страницы -->
@yield('content')

</body>
</html>
