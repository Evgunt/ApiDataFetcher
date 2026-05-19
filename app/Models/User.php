<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Foundation\Auth\User as Authenticatable;
    use Illuminate\Notifications\Notifiable;

    class User extends Authenticatable
    {
        use HasFactory, Notifiable;

        /**
         * Атрибуты, которые можно заполнять массово.
         */
        protected $fillable = [
            'name',
            'email',
            'password',
        ];

        /**
         * Атрибуты, которые должны быть скрыты в JSON-ответах.
         */
        protected $hidden = [
            'password',
            'remember_token',
        ];

        /**
         * Автоматическое приведение типов.
         */
        protected $casts = [
            'email_verified_at' => 'datetime',
            'password' => 'hashed', // Автоматически хэширует пароль в Laravel 11
        ];
    }
