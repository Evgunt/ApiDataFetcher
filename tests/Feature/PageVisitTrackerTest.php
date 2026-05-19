<?php

    namespace Tests\Feature;

    use Illuminate\Foundation\Testing\RefreshDatabase;
    use Illuminate\Support\Facades\Http;
    use App\Models\PageVisit;
    use Tests\TestCase;

    class PageVisitTrackerTest extends TestCase
    {
        use RefreshDatabase; // Сбрасываем тестовую БД перед каждым тестом

        /**
         * Тест успешного сохранения визита с реальным IP и вычислением города.
         */
        public function test_it_saves_page_visit_and_fetches_city_via_geoip(): void
        {
            // 1. Изолируем внешний сетевой запрос к GeoIP-сервису (Mocking)
            // Имитируем, что для IP '8.8.8.8' сервис возвращает город 'Нью-Йорк'
            Http::fake([
                '://ip-api.com*' => Http::response([
                    'status' => 'success',
                    'city'   => 'Нью-Йорк'
                ], 200)
            ]);

            // 2. Формируем полезную нагрузку, которую присылает наш frontend-трекер (tracker.js)
            $payload = [
                'device'            => 'Desktop',
                'screen_resolution' => '1920x1080',
                'current_url'       => 'https://example.com'
            ];

            // 3. Делаем POST-запрос на наш API-эндпоинт приема статистики
            // Передаем кастомный серверный заголовок REMOTE_ADDR, чтобы имитировать реальный внешний IP-адрес
            $response = $this->withServerVariables(['REMOTE_ADDR' => '8.8.8.8'])
                ->postJson('/api/visit', $payload);

            // 4. Проверяем HTTP-статус ответа бэкенда (201 Created)
            $response->assertStatus(201)
                ->assertJson([
                    'success' => true
                ]);

            // 5. Проверяем, что запись успешно сохранилась в SQLite и город определился верно
            $this->assertDatabaseHas('page_visits', [
                'ip'                => '8.8.8.8',
                'city'              => 'Нью-Йорк',
                'device'            => 'Desktop',
                'screen_resolution' => '1920x1080',
                'current_url'       => 'https://example.com'
            ]);
        }

        /**
         * Тест логики обработки ошибок, если внешний GeoIP сервис недоступен или упал.
         */
        public function test_it_handles_geoip_service_failure_gracefully(): void
        {
            // Имитируем падение сервера ip-api.com (500 Error)
            Http::fake([
                '://ip-api.com*' => Http::response('Server Error', 500)
            ]);

            $payload = [
                'device'            => 'Mobile',
                'screen_resolution' => '375x812',
                'current_url'       => 'https://example.com'
            ];

            // Делаем запрос от внешнего IP
            $response = $this->withServerVariables(['REMOTE_ADDR' => '1.1.1.1'])
                ->postJson('/api/visit', $payload);

            // Бэкенд все равно обязан вернуть статус 201, не прерывая работу из-за внешней ошибки
            $response->assertStatus(201);

            // В базу должна записаться дефолтная строка-заглушка вместо названия города
            $this->assertDatabaseHas('page_visits', [
                'ip'   => '1.1.1.1',
                'city' => 'Не определен'
            ]);
        }
    }
