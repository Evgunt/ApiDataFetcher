<?php

    namespace App\Http\Controllers\Api;

    use App\Http\Controllers\Controller;
    use App\Models\CryptoPrice;
    use App\Http\Resources\CryptoPriceResource;
    use Illuminate\Http\JsonResponse;

    class CryptoPriceController extends Controller
    {
        /**
         * Возвращает список всех сохраненных курсов криптовалют.
         */
        public function index(): JsonResponse
        {
            $prices = CryptoPrice::all();
            // Передаем коллекцию через ресурс для единого формата JSON
            return response()->json(
                CryptoPriceResource::collection($prices)
            );
        }
    }
