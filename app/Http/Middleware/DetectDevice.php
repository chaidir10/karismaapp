<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Jenssegers\Agent\Agent;

class DetectDevice
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        $agent = new Agent();

        $isMobile = $agent->isMobile() || $agent->isTablet();
        $deviceType = $isMobile ? 'mobile' : 'desktop';

        // Log untuk debug
        Log::info('DetectDevice:', [
            'user_id' => $user->id ?? null,
            'role' => $user->role ?? 'guest',
            'device' => $deviceType,
            'path' => $request->path(),
        ]);

        if ($user) {
            switch ($user->role) {
                case 'pegawai':
                    // Pegawai harus di route pegawai/*
                    if (!$request->is('pegawai/*')) {
                        return redirect()->route('pegawai.dashboard');
                    }
                    break;

                case 'admin':
                case 'superadmin':
                    if ($deviceType === 'desktop') {
                        // Desktop → normal sesuai role
                        if ($user->role === 'admin' && !$request->is('admin/*')) {
                            return redirect()->route('admin.dashboard');
                        }
                        if ($user->role === 'superadmin' && !$request->is('superadmin/*')) {
                            return redirect()->route('superadmin.dashboard');
                        }
                    } else {
                        // Mobile → arahkan ke PWA pegawai sekali saja
                        if (!$request->is('pegawai/*')) {
                            if (!$request->session()->get('mobile_redirected', false)) {
                                $request->session()->put('mobile_redirected', true);
                                return redirect()->route('pegawai.dashboard');
                            }
                        }
                    }
                    break;
            }
        }

        return $next($request);
    }
}
