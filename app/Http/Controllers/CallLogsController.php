<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CallLogsController extends Controller
{
    /**
     * Display a listing of call logs.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $dealershipId = $request->user()->dealership_id;
        $department = $request->query('department');
        
        // Build query with optional department filter
        $query = \App\Models\CallLog::where('dealership_id', $dealershipId);
        
        // Apply department filter if specified
        if ($department) {
            $query->where('department', $department);
        }
        
        $callLogs = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();
        
        return inertia('CallLogs/Index', [
            'callLogs' => $callLogs,
            'filters' => [
                'department' => $department,
            ],
        ]);
    }
    
    /**
     * Display the specified call log.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Inertia\Response
     */
    public function show(Request $request, $id)
    {
        $dealershipId = $request->user()->dealership_id;
        
        $callLog = \App\Models\CallLog::where('dealership_id', $dealershipId)
            ->findOrFail($id);
        
        return inertia('CallLogs/Show', [
            'callLog' => $callLog,
        ]);
    }
    
    /**
     * Get call logs for the dashboard.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function forDashboard(Request $request)
    {
        $dealershipId = $request->user()->dealership_id;
        
        $callLogs = \App\Models\CallLog::where('dealership_id', $dealershipId)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'call_id' => $log->call_id,
                    'status' => $log->status,
                    'direction' => $log->direction,
                    'caller_number' => $log->caller_number,
                    'duration' => $log->duration,
                    'call_started_at' => $log->call_started_at,
                    'call_ended_at' => $log->call_ended_at,
                ];
            });
        
        $stats = [
            'total' => \App\Models\CallLog::where('dealership_id', $dealershipId)->count(),
            'completed' => \App\Models\CallLog::where('dealership_id', $dealershipId)->where('status', 'completed')->count(),
            'in_progress' => \App\Models\CallLog::where('dealership_id', $dealershipId)->where('status', 'in-progress')->count(),
            'failed' => \App\Models\CallLog::where('dealership_id', $dealershipId)->where('status', 'failed')->count(),
        ];
        
        return response()->json([
            'callLogs' => $callLogs,
            'stats' => $stats,
        ]);
    }
}
