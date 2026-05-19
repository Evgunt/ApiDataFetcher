<?php

namespace App\Console\Commands;

use App\Models\CryptoPrice;
use App\Services\BinanceService;
use Illuminate\Console\Command;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;

#[Signature('crypto:fetch-prices')]
#[Description('Получает актуальные курсы криптовалют с Binance и сохраняет в БД')]
class FetchCryptoPrices extends Command
{
    public function __construct(protected BinanceService $binanceService)
    {
        parent::__construct();
    }

    public function handle() : int
    {
        $this->info('Запуск синхронизации с Binance...');

        // Массив пар, которые мы хотим отслеживать
        $targetSymbols = ['BTCUSDT', 'ETHUSDT', 'BNBUSDT'];

        try {
            $prices = $this->binanceService->getPrices($targetSymbols);

            foreach ($prices as $item) {
                CryptoPrice::updateOrCreate(
                    ['symbol' => $item['symbol']], // Ищем по уникальному символу
                    ['price'  => $item['price']]  // Обновляем цену и timestamps
                );
            }

            $this->info('Курсы валют успешно обновлены.');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Произошла ошибка: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
