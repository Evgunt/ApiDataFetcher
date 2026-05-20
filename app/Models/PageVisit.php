<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    class PageVisit extends Model
    {
        // Отключаем автоматический updated_at
        public const UPDATED_AT = null;

        protected $fillable = [
            'ip',
            'city',
            'device',
            'address',
            'screen_resolution',
            'current_url',
        ];
    }
