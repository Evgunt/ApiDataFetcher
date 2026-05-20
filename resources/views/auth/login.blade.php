@extends('layouts.app')

@section('title', 'Вход в панель статистики')
@section('body_class', 'h-screen flex items-center justify-center')

@section('content')
    <div class="bg-white p-8 rounded-lg shadow-md w-96">
        <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Авторизация</h2>

        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="/login" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                <input type="email" name="email" class="w-full p-2 border rounded focus:outline-blue-500" value="admin@stats.ru" required>
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Пароль</label>
                <input type="password" name="password" class="w-full p-2 border rounded focus:outline-blue-500" value="admin123" required>
            </div>
            <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded font-bold hover:bg-blue-600 transition">Войти</button>
        </form>
    </div>
@endsection
