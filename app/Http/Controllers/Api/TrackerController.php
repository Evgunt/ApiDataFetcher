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
            $country = $region = '';
            // 2. Если это не локальный адрес, запрашиваем город через бесплатное API
            if ($ip !== '127.0.0.1' && $ip !== '::1') {
                try {
                    $geoResponse = Http::baseUrl('https://ipapi.co')->get("/{$ip}/json");
                    if ($geoResponse->successful()) {
                        $city = $geoResponse->json('city') ?? 'Не определен';
                        $country = $geoResponse->json('country_name');
                        $region = $geoResponse->json('region');
                    } else {
                        $city = 'Не определен';
                    }
                } catch (\Exception $e) {
                    $city = 'Ошибка определения'; // Защита на случай недоступности сервиса
                }
            }
            $address = implode(', ', array_filter([$country, $region, $city]));

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
                'address'           => $address,
                'device'            => $validated['device'],
                'screen_resolution' => $validated['screen_resolution'],
                'current_url'       => $validated['current_url'],
            ]);

            return response()->json(['success' => true, 'id' => $visit->id], 201);
        }
    }
