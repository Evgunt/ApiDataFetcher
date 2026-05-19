<?php

    namespace Database\Seeders;

    use Illuminate\Database\Seeder;
    use App\Models\User;
    use Illuminate\Support\Facades\Hash;

    class AdminUserSeeder extends Seeder
    {
        public function run(): void
        {
            User::updateOrCreate(
                ['email' => 'admin@stats.ru'],
                [
                    'name' => 'Администратор',
                    'password' => Hash::make('admin123'), // Надежный хэш пароля
                ]
            );
        }
    }
