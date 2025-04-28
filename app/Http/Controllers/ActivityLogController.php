<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index()
    {
        $logs = Activity::with('causer')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'log_name' => $log->log_name,
                    'description' => $log->description,
                    'subject_type' => $log->subject_type,
                    'subject_id' => $log->subject_id,
                    'causer' => $log->causer ? [
                        'name' => $log->causer->name,
                        'email' => $log->causer->email,
                        'id' => $log->causer->id,
                    ] : null,
                    'properties' => $log->properties,
                    'event' => $log->event,
                    'created_at' => $log->created_at
                ];
            });

        return inertia('ActivityLog', [
            'logs' => $logs
        ]);
    }
}
