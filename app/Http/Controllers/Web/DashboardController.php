<?php

    namespace App\Http\Controllers\Web;

    use App\Http\Controllers\Controller;
    use App\Models\PageVisit;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\DB;

    class DashboardController extends Controller
    {
        // Показ формы логина
        public function showLogin()
        {
            if (Auth::check()) return redirect()->route('dashboard');
            return view('auth.login');
        }

        // Обработка авторизации
        public function login(Request $request)
        {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();
                return redirect()->intended('dashboard');
            }

            return back()->withErrors([
                'email' => 'Неверный email или пароль.',
            ]);
        }

        // Выход из системы
        public function logout(Request $request)
        {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login');
        }

        // Главная панель со статистикой и графиками
        public function index()
        {
            // 1. Сбор данных для графика по часам (за последние 24 часа)
            // Группируем по часам на уровне базы SQLite
            $hourlyData = PageVisit::select(
                DB::raw("strftime('%H:00', created_at) as hour"),
                DB::raw("COUNT(DISTINCT ip) as unique_visits") // Считаем именно УНИКАЛЬНЫЕ IP за час по ТЗ
            )
                ->where('created_at', '>=', now()->subDay())
                ->groupBy('hour')
                ->orderBy('hour', 'asc')
                ->get();

            // 2. Сбор данных для круговой диаграммы по городам
            $cityData = PageVisit::select('city', DB::raw('COUNT(*) as total'))
                ->groupBy('city')
                ->orderBy('total', 'desc')
                ->take(10) // Топ-10 городов
                ->get();

            return view('dashboard.index', compact('hourlyData', 'cityData'));
        }
    }
