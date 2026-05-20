@extends('layouts.app')

@section('title', 'Панель статистики')

@push('scripts')
    <!-- Подключаем Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
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
        document.addEventListener("DOMContentLoaded", function () {
            const hourlyRaw = {!! json_encode($hourlyData) !!};
            const cityRaw = {!! json_encode($cityData) !!};

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
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            }

            const cityCanvas = document.getElementById('cityChart');
            if (cityCanvas) {
                new Chart(cityCanvas, {
                    type: 'pie',
                    data: {
                        labels: cityRaw.map(item => item.city),
                        datasets: [{
                            data: cityRaw.map(item => item.total),
                            backgroundColor: ['#ef4444', '#f97316', '#f59e0b', '#10b981', '#06b6d4', '#3b82f6', '#6366f1', '#8b5cf6', '#ec4899']
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
@endsection
