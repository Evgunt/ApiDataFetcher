<?php

    namespace Tests\Feature;

    use Illuminate\Foundation\Testing\RefreshDatabase;
    use Illuminate\Support\Facades\Http;
    use App\Models\CryptoPrice;
    use Tests\TestCase;

    class FetchCryptoPricesTest extends TestCase
    {
        use RefreshDatabase; // Автоматически сбрасывает базу данных между тестами

        public function test_it_fetches_prices_from_binance_and_saves_them_to_db(): void
        {
            // 1. Изолируем сетевой запрос (Mocking)
            Http::fake([
                '://api.binance.com*' => Http::response([
                    ['symbol' => 'BTCUSDT', 'price' => '65000.00000000'],
                    ['symbol' => 'ETHUSDT', 'price' => '35000.00000000'],
                ], 200)
            ]);

            // 2. Запускаем консольную команду
            $this->artisan('crypto:fetch-prices')
                ->assertSuccessful(); // Проверяем, что вернулся Command::SUCCESS

            // 3. Проверяем, что данные появились в тестовой БД
            $this->assertDatabaseHas('crypto_prices', [
                'symbol' => 'BTCUSDT',
                'price' => 65000.00
            ]);

            $this->assertDatabaseHas('crypto_prices', [
                'symbol' => 'ETHUSDT',
                'price' => 35000.00
            ]);

            // Проверяем, что сохранилось именно 2 записи
            $this->assertCount(2, CryptoPrice::all());
        }
    }
