<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Agent;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $agent = new Agent();
        $device = ($agent->isTablet() || $agent->isMobile()) ? 'mobile' : 'desktop';

        $userRole = strtolower($user->role);

        // ✅ Pegawai route → admin/superadmin boleh ikut masuk kalau mobile
        if (in_array('pegawai', $roles)) {
            if (in_array($userRole, ['pegawai','admin','superadmin'])) {
                return $next($request);
            }
        }

        // ✅ Normal role check
        if (in_array($userRole, array_map('strtolower', $roles))) {
            return $next($request);
        }

        // ✅ Redirect fallback sesuai role & device
        if ($userRole === 'superadmin') {
            return $device === 'desktop'
                ? redirect()->route('superadmin.dashboard')
                : redirect()->route('pegawai.dashboard');
        }

        if ($userRole === 'admin') {
            return $device === 'desktop'
                ? redirect()->route('admin.dashboard')
                : redirect()->route('pegawai.dashboard');
        }

        return redirect()->route('pegawai.dashboard');
    }
}
