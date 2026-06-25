<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeviceIssue;
use Illuminate\Http\Request;

class DeviceIssueController extends Controller
{
    public function index()
    {
        $activeIssues = DeviceIssue::with('user')
            ->whereNull('resolved_at')
            ->orderBy('reported_at', 'desc')
            ->get()
            ->groupBy('user_id');

        $resolvedIssues = DeviceIssue::with('user')
            ->whereNotNull('resolved_at')
            ->orderBy('resolved_at', 'desc')
            ->limit(50)
            ->get();

        return view('admin.device-issues', compact('activeIssues', 'resolvedIssues'));
    }

    public function resolve($id)
    {
        $issue = DeviceIssue::findOrFail($id);
        $issue->update(['resolved_at' => now()]);

        if (request()->ajax()) {
            return response()->json(['ok' => true]);
        }
        return redirect()->back()->with('success', 'Ditandai selesai');
    }

    public function resolveUser($userId)
    {
        DeviceIssue::where('user_id', $userId)
            ->whereNull('resolved_at')
            ->update(['resolved_at' => now()]);

        if (request()->ajax()) {
            return response()->json(['ok' => true]);
        }
        return redirect()->back()->with('success', 'Semua kendala user ditandai selesai');
    }

    public static function report(Request $request)
    {
        $request->validate([
            'type' => 'required|string|in:camera_blocked,camera_error,location_blocked,location_error,face_detection_error',
        ]);

        DeviceIssue::report(
            auth()->id(),
            $request->input('type'),
            $request->userAgent()
        );

        return response()->json(['ok' => true]);
    }
}
