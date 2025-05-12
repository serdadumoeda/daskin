<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Import User model

class MainDashboardController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            $user = Auth::user();
            // Arahkan ke dashboard departemen pertama yang bisa diakses pengguna
            // Superadmin diarahkan ke dashboard Itjen sebagai default, atau dashboard global jika ada
            if ($user->isSuperAdmin()) {
                return redirect()->route('inspektorat.dashboard'); 
            }
            if ($user->isItjen()) {
                return redirect()->route('inspektorat.dashboard');
            }
            if ($user->isSekjen()) {
                return redirect()->route('sekretariat-jenderal.dashboard');
            }
            if ($user->isBinapenta()) {
                return redirect()->route('binapenta.dashboard');
            }
            if ($user->isBinalavotas()) {
                return redirect()->route('binalavotas.dashboard');
            }
            if ($user->isBinwasnaker()) {
                return redirect()->route('binwasnaker.dashboard');
            }
            if ($user->isPhi()) {
                return redirect()->route('phi.dashboard');
            }
            if ($user->isBarenbang()) {
                return redirect()->route('barenbang.dashboard');
            }
            // Jika pengguna memiliki peran 'user' atau peran lain yang tidak memiliki dashboard spesifik
            if (view()->exists('dashboard.default')) { // Buat view dashboard.default jika perlu
                return view('dashboard.default');
            }
            // Fallback jika tidak ada peran yang cocok atau view default tidak ada
            abort(403, 'Anda tidak memiliki dashboard yang ditetapkan.');
        }
        return redirect()->route('login'); // Seharusnya tidak sampai sini jika middleware auth aktif
    }
}
