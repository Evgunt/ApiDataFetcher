<?php

    namespace App\Http\Controllers\Api;

    use App\Http\Controllers\Controller;
    use App\Models\PageVisit;
    use Illuminate\Http\Request;
    use Illuminate\Http\JsonResponse;
    use Illuminate\Support\Facades\Http;

    class TrackerController extends Controller
    {
        /**
         * Принимает данные от JS-трекера, определяет IP/Город и сохраняет в БД.
         */
        public function store(Request $request): JsonResponse
        {
            // 1. Получаем реальный IP-адрес посетителя
            $ip = $request->ip();
            $city = 'Локальный хост';

            // 2. Если это не локальный адрес, запрашиваем город через бесплатное GeoIP API
            if ($ip !== '127.0.0.1' && $ip !== '::1') {
                try {
                    // Делаем быстрый запрос к ip-api (таймаут 3 секунды, чтобы не вешать бэкенд)
                    $geoResponse = Http::timeout(3)->get("http://ip-api.com{$ip}?lang=ru");

                    if ($geoResponse->successful() && $geoResponse->json('status') === 'success') {
                        $city = $geoResponse->json('city') ?? 'Не определен';
                    } else {
                        $city = 'Не определен';
                    }
                } catch (\Exception $e) {
                    $city = 'Ошибка определения'; // Защита на случай недоступности GeoIP сервиса
                }
            }

            // 3. Валидируем входящие данные от JS-скрипта
            $validated = $request->validate([
                'device'            => 'required|string|max:20',
                'screen_resolution' => 'nullable|string|max:20',
                'current_url'       => 'nullable|string|max:255',
            ]);

            // 4. Записываем посещение в базу данных SQLite
            $visit = PageVisit::create([
                'ip'                => $ip,
                'city'              => $city,
                'device'            => $validated['device'],
                'screen_resolution' => $validated['screen_resolution'],
                'current_url'       => $validated['current_url'],
            ]);

            return response()->json(['success' => true, 'id' => $visit->id], 201);
        }
    }
