<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use App\Models\UserLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OperatorTrackingController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('nip', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $threshold = now()->subMinutes(15);
            $onlineUserIds = ActivityLog::where('created_at', '>=', $threshold)
                ->distinct()
                ->pluck('user_id');

            if ($request->status === 'online') {
                $query->whereIn('id', $onlineUserIds);
            } else {
                $query->whereNotIn('id', $onlineUserIds);
            }
        }

        $users = $query->orderBy('name')->paginate(20)->withQueryString();

        $userIds = $users->pluck('id');
        $lastActivities = ActivityLog::whereIn('user_id', $userIds)
            ->select('user_id', DB::raw('MAX(created_at) as last_seen'), DB::raw('MAX(ip_address) as last_ip'))
            ->groupBy('user_id')
            ->pluck('last_seen', 'user_id');

        $lastDetails = ActivityLog::whereIn('user_id', $userIds)
            ->whereIn('created_at', function ($q) use ($userIds) {
                $q->select(DB::raw('MAX(created_at)'))
                  ->from('activity_logs')
                  ->whereIn('user_id', $userIds)
                  ->groupBy('user_id');
            })
            ->get()
            ->keyBy('user_id');

        $onlineThreshold = now()->subMinutes(15);

        return view('operator.tracking', compact('users', 'lastActivities', 'lastDetails', 'onlineThreshold'));
    }

    public function detail(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        $logs = ActivityLog::where('user_id', $userId)
            ->latest('created_at')
            ->take(50)
            ->get();

        $devices = ActivityLog::where('user_id', $userId)
            ->select('device_type', 'browser', 'platform', 'ip_address', DB::raw('MAX(created_at) as last_used'), DB::raw('COUNT(*) as usage_count'))
            ->groupBy('device_type', 'browser', 'platform', 'ip_address')
            ->orderByDesc('last_used')
            ->take(10)
            ->get();

        $ipAddresses = ActivityLog::where('user_id', $userId)
            ->select('ip_address', DB::raw('COUNT(*) as count'), DB::raw('MAX(created_at) as last_used'))
            ->groupBy('ip_address')
            ->orderByDesc('count')
            ->take(10)
            ->get();

        $dailyActivity = ActivityLog::where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays(7))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('operator.tracking-detail', compact('user', 'logs', 'devices', 'ipAddresses', 'dailyActivity'));
    }

    public function ping(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy' => 'nullable|numeric',
        ]);

        UserLocation::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'accuracy' => $request->accuracy,
                'updated_at' => now(),
            ]
        );

        return response()->json(['ok' => true]);
    }

    public function liveMap()
    {
        return view('operator.live-map');
    }

    public function locationsJson()
    {
        $locations = UserLocation::with('user:id,name,nip,jabatan,role')
            ->whereHas('user', function ($q) {
                $q->where('is_tester', false)->where('role', '!=', 'operator');
            })
            ->get()
            ->map(function ($loc) {
                return [
                    'user_id' => $loc->user_id,
                    'name' => $loc->user->name ?? '-',
                    'nip' => $loc->user->nip ?? '-',
                    'jabatan' => $loc->user->jabatan ?? '-',
                    'role' => $loc->user->role ?? '-',
                    'lat' => $loc->latitude,
                    'lng' => $loc->longitude,
                    'accuracy' => $loc->accuracy,
                    'updated_at' => $loc->updated_at->format('d/m/Y H:i:s'),
                    'minutes_ago' => $loc->updated_at->diffInMinutes(now()),
                ];
            });

        return response()->json($locations);
    }
}
