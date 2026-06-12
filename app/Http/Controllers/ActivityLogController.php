<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        // Only admins and project managers can view audit logs
        abort_unless(
            $request->user()->hasRole(['admin', 'project_manager']),
            403,
            'Access denied.'
        );

        $logs = ActivityLog::with('user')
            ->latest()
            ->paginate(25);

        return view('activity-logs.index', compact('logs'));
    }
}
