<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Jenssegers\Agent\Agent;

class AuthenticatedSessionController extends Controller
{
    public function __construct()
    {
        // Middleware untuk mencegah back button menampilkan halaman login
        $this->middleware('preventbackhistory')->only(['create', 'store']);
    }

    /**
     * Display the login view.
     */
    public function create(): View|RedirectResponse
    {
        // Jika user sudah login, lempar ke dashboard sesuai role
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = $request->user();
        $agent = new Agent();
        $isMobile = $agent->isMobile() || $agent->isTablet();

        if ($isMobile) {
            // Paksa login dengan remember me (infinite session)
            Auth::login($user, true);

            // Semua role diarahkan ke dashboard mobile (pegawai)
            return redirect()->route('pegawai.dashboard');
        }

        // Desktop → redirect sesuai role
        return $this->redirectByRole($user);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Setelah logout, user bisa akses login kembali
        return redirect()->route('login');
    }

    /**
     * Redirect user berdasarkan role.
     */
    private function redirectByRole($user): RedirectResponse
    {
        return match ($user->role) {
            'pegawai' => redirect()->route('pegawai.dashboard'),
            'admin' => redirect()->route('admin.dashboard'),
            'superadmin' => redirect()->route('superadmin.dashboard'),
            default => redirect()->route('pegawai.dashboard'),
        };
    }
}
