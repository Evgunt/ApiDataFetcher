<?php

    namespace App\Services;

    use Illuminate\Support\Facades\Http;
    use Illuminate\Support\Collection;

    class BinanceService
    {
        /**
         * Пример приватного запроса с авторизацией и цифровой подписью HMAC SHA256.
         *
         * @deprecated Демонстрационный метод. Не используется в основном бизнес-процессе решения ТЗ.
         * Заведен исключительно для демонстрации навыков работы с секретными ключами и хэшированием.
         */
        public function getAccountInfo(): array
        {
            $apiKey = config('services.binance.key');
            $apiSecret = config('services.binance.secret');

            // Binance требует timestamp (в миллисекундах) для защиты от повторных запросов (Replay Attacks)
            $timestamp = now()->getTimestampMs();

            // Формируем query-строку, которую нужно подписать
            $queryString = "timestamp={$timestamp}";

            // Генерируем хэш-подпись с использованием Secret Key
            $signature = hash_hmac('sha256', $queryString, $apiSecret);

            // Отправляем защищенный запрос
            $response = Http::baseUrl('https://api.binance.com')
                // Отключаем проверку SSL только на локальной машине, чтобы код не падал
                ->withOptions([
                    'verify' => !app()->environment('local'),
                ])
                // Передаем API-ключ в заголовке
                ->withHeaders([
                    'X-MBX-APIKEY' => $apiKey, // Передаем API-ключ в заголовке
                ])
                ->get("/api/v3/account?{$queryString}&signature={$signature}");

            if ($response->failed()) {
                throw new \RuntimeException(
                    "Ошибка авторизации Binance API (Статус {$response->status()}): " . $response->body()
                );
            }

            return $response->json();
        }
        /**
         * Получает текущие цены для переданных торговых пар.
         * Пример ответа Binance: [{"symbol": "BTCUSDT", "price": "65000.00"}, ...]
         */
        public function getPrices(array $symbols): Collection
        {
            // Формируем параметры в формате JSON-массива, как требует Binance для фильтрации
            $response = Http::baseUrl('https://api.binance.com')
                // Отключаем проверку SSL только на локальной машине, чтобы код не падал
                ->withOptions([
                    'verify' => !app()->environment('local'),
                ])
                ->get('/api/v3/ticker/price', [
                    'symbols' => json_encode(array_values($symbols)),
                ]);

            if ($response->failed()) {
                throw new \RuntimeException(
                    "Ошибка Binance API (Статус {$response->status()}): " . $response->body()
                );
            }

            return collect($response->json());
        }
    }
