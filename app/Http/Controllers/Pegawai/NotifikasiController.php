<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\NotificationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    public function index()
    {
        $logs = NotificationLog::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get(['id', 'title', 'body', 'url', 'tag', 'is_read', 'created_at']);

        return response()->json($logs);
    }

    public function unreadCount()
    {
        $count = NotificationLog::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    public function markRead($id)
    {
        NotificationLog::where('id', $id)
            ->where('user_id', Auth::id())
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json(['ok' => true]);
    }

    public function markAllRead()
    {
        NotificationLog::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json(['ok' => true]);
    }

    public function clear()
    {
        NotificationLog::where('user_id', Auth::id())->delete();
        return response()->json(['ok' => true]);
    }

    // Dipanggil dari page JS saat ada notifikasi lokal (pengumuman bell)
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body'  => 'nullable|string|max:500',
            'url'   => 'nullable|string|max:500',
            'tag'   => 'nullable|string|max:100',
        ]);

        $log = NotificationLog::create([
            'user_id'    => Auth::id(),
            'title'      => $request->title,
            'body'       => $request->body ?? '',
            'url'        => $request->url ?? '/pegawai/dashboard',
            'tag'        => $request->tag ?? '',
            'is_read'    => false,
            'created_at' => now(),
        ]);

        return response()->json(['id' => $log->id]);
    }
}
