<?php
    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    class CryptoPrice extends Model
    {
        // Разрешаем массовое заполнение для полей symbol и price
        protected $fillable = [
            'symbol',
            'price',
        ];
    }
