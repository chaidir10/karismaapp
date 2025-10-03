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
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = $request->user();
        $agent = new Agent();

        $isMobile = $agent->isMobile() || $agent->isTablet();

        if ($isMobile) {
            // Paksa login dengan remember me (infinite session)
            Auth::login($user, true);

            // Semua role diarahkan ke view pegawai di mobile
            return redirect()->route('pegawai.dashboard');
        }

        // Kalau desktop → ikuti role
        if ($user->role === 'pegawai') {
            return redirect()->route('pegawai.dashboard');
        } elseif ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'superadmin') {
            return redirect()->route('superadmin.dashboard');
        }

        return redirect()->intended('/'); // fallback
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
