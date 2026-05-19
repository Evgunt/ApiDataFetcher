<?php

    namespace App\Http\Resources;

    use Illuminate\Http\Request;
    use Illuminate\Http\Resources\Json\JsonResource;

    class CryptoPriceResource extends JsonResource
    {
        public function toArray(Request $request): array
        {
            return [
                'symbol'     => $this->symbol,
                'price'      => (float) $this->price, // Приводим к числу из строки decimal
                'updated_at' => $this->updated_at->toIso8601String(),
            ];
        }
    }

