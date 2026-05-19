<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Панель статистики</title>
    <!-- Библиотека графиков Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 font-sans">
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

<div class="max-w-6xl mx-auto px-4 grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
    <!-- График посещений по часам -->
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-bold text-gray-700 mb-4">Уникальные посещения по часам (За 24ч)</h3>
        <div class="h-80 flex items-center justify-center">
            <canvas id="hourlyChart"></canvas>
        </div>
    </div>

    <!-- Круговая диаграмма по городам -->
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-bold text-gray-700 mb-4">Разбиение по городам</h3>
        <div class="h-80 flex items-center justify-center">
            <canvas id="cityChart"></canvas>
        </div>
    </div>
</div>
<script>
    // Ждем полной загрузки DOM-дерева и всех внешних скриптов (Chart.js)
    document.addEventListener("DOMContentLoaded", function () {

        // Данные из Laravel Blade-директив
        const hourlyRaw = {!! json_encode($hourlyData) !!};
        const cityRaw = {!! json_encode($cityData) !!};

        // 1. Настройка горизонтального графика по часам
        const hourlyCanvas = document.getElementById('hourlyChart');
        if (hourlyCanvas) {
            new Chart(hourlyCanvas, {
                type: 'bar',
                data: {
                    labels: hourlyRaw.map(item => item.hour),
                    datasets: [{
                        label: 'Уникальные посещения',
                        data: hourlyRaw.map(item => item.unique_visits),
                        backgroundColor: 'rgba(59, 130, 246, 0.7)',
                        borderColor: 'rgb(59, 130, 246)',
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y', // Наше строгое условие ТЗ: X - количество, Y - время
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }

        // 2. Настройка круговой диаграммы по городам
        const cityCanvas = document.getElementById('cityChart');
        if (cityCanvas) {
            new Chart(cityCanvas, {
                type: 'pie',
                data: {
                    labels: cityRaw.map(item => item.city),
                    datasets: [{
                        data: cityRaw.map(item => item.total),
                        backgroundColor: [
                            '#ef4444', '#f97316', '#f59e0b', '#10b981',
                            '#06b6d4', '#3b82f6', '#6366f1', '#8b5cf6', '#ec4899'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }
    });
</script>
</body>
</html>
