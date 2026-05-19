<?php

    namespace Tests\Feature;

    use Illuminate\Foundation\Testing\RefreshDatabase;
    use App\Models\CryptoPrice;
    use Tests\TestCase;

    class CryptoPriceApiTest extends TestCase
    {
        use RefreshDatabase;

        public function test_it_returns_crypto_prices_in_correct_json_format(): void
        {
            // 1. Создаем тестовые данные в БД
            CryptoPrice::create(['symbol' => 'BTCUSDT', 'price' => 60000.50]);
            CryptoPrice::create(['symbol' => 'BNBUSDT', 'price' => 550.00]);

            // 2. Делаем GET-запрос на наш API-роут
            $response = $this->getJson('/api/prices');

            // 3. Проверяем статус ответа и структуру JSON
            $response->assertStatus(200)
                ->assertJsonStructure([
                    '*' => [
                        'symbol',
                        'price',
                        'updated_at'
                    ]
                ])
                // Проверяем наличие конкретных данных в ответе
                ->assertJsonFragment(['symbol' => 'BTCUSDT', 'price' => 60000.50])
                ->assertJsonFragment(['symbol' => 'BNBUSDT', 'price' => 550.00]);
        }
    }
