<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivityLog;
use Jenssegers\Agent\Agent;

class LogActivity
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (Auth::check() && !$request->isMethod('OPTIONS')) {
            $this->log($request);
        }

        return $response;
    }

    protected function log(Request $request): void
    {
        $agent = new Agent();
        $agent->setUserAgent($request->userAgent());

        $action = $this->resolveAction($request);
        if (!$action) {
            return;
        }

        try {
            ActivityLog::create([
                'user_id'     => Auth::id(),
                'action'      => $action,
                'description' => $this->resolveDescription($request, $action),
                'url'         => $request->fullUrl(),
                'method'      => $request->method(),
                'ip_address'  => $request->ip(),
                'user_agent'  => $request->userAgent(),
                'device_type' => $agent->isTablet() ? 'tablet' : ($agent->isMobile() ? 'mobile' : 'desktop'),
                'browser'     => $agent->browser() . ' ' . $agent->version($agent->browser()),
                'platform'    => $agent->platform() . ' ' . $agent->version($agent->platform()),
                'created_at'  => now(),
            ]);
        } catch (\Throwable $e) {
            // silent fail
        }
    }

    protected function resolveAction(Request $request): ?string
    {
        $path = $request->path();
        $method = $request->method();

        if ($path === 'login' && $method === 'POST') return 'login';
        if ($path === 'logout' && $method === 'POST') return 'logout';

        if (str_contains($path, '/presensi') && $method === 'POST') return 'presensi';
        if (str_contains($path, '/pengajuan') && $method === 'POST') return 'pengajuan';

        if ($method === 'GET') return 'page_view';
        if ($method === 'POST') return 'create';
        if ($method === 'PUT' || $method === 'PATCH') return 'update';
        if ($method === 'DELETE') return 'delete';

        return null;
    }

    protected function resolveDescription(Request $request, string $action): string
    {
        $path = $request->path();

        return match ($action) {
            'login'     => 'Login ke sistem',
            'logout'    => 'Logout dari sistem',
            'presensi'  => 'Melakukan presensi',
            'pengajuan' => 'Mengajukan presensi',
            'page_view' => 'Mengakses ' . $path,
            'create'    => 'Membuat data di ' . $path,
            'update'    => 'Mengubah data di ' . $path,
            'delete'    => 'Menghapus data di ' . $path,
            default     => $action,
        };
    }
}
