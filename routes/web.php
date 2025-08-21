<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CallLogsController;
use App\Http\Controllers\KnowledgeBaseController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\Api\VapiWebhookController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Direct webhook route at root URL with CSRF protection explicitly disabled
Route::post('/', [WebhookController::class, 'handleWebhook'])->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

// Regular webhook route
Route::post('/webhook', [WebhookController::class, 'handleWebhook'])->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Webhook routes are now in routes/webhooks.php




Route::get('/dashboard', function (\Illuminate\Http\Request $request) {
    $dealershipId = auth()->user()->dealership_id;
    $department = $request->query('department');
    
    // Get paginated call logs for the table with optional department filter
    $query = \App\Models\CallLog::where('dealership_id', $dealershipId);
    
    // Apply department filter if specified
    if ($department) {
        $query->where('department', $department);
    }
    
    $callLogs = $query->orderBy('created_at', 'desc')
        ->paginate(10)
        ->withQueryString();
    
    // Get statistics for the dashboard widgets
    $totalCalls = \App\Models\CallLog::where('dealership_id', $dealershipId)->count();
    $completedCalls = \App\Models\CallLog::where('dealership_id', $dealershipId)
        ->where('status', 'completed')
        ->count();
    
    // Calculate percentage of calls answered
    $percentAnswered = $totalCalls > 0 ? round(($completedCalls / $totalCalls) * 100) : 0;
    
    // Get total duration in minutes
    $totalMinutes = \App\Models\CallLog::where('dealership_id', $dealershipId)
        ->sum('duration') / 60; // Convert seconds to minutes
    $totalMinutes = round($totalMinutes, 1); // Round to 1 decimal place
    
    // Calculate minutes by department
    $salesMinutes = \App\Models\CallLog::where('dealership_id', $dealershipId)
        ->where('department', 'sales')
        ->sum('duration') / 60; // Convert seconds to minutes
    $salesMinutes = round($salesMinutes, 1); // Round to 1 decimal place
    
    $serviceMinutes = \App\Models\CallLog::where('dealership_id', $dealershipId)
        ->where('department', 'service')
        ->sum('duration') / 60; // Convert seconds to minutes
    $serviceMinutes = round($serviceMinutes, 1); // Round to 1 decimal place
    
    $partsMinutes = \App\Models\CallLog::where('dealership_id', $dealershipId)
        ->where('department', 'parts')
        ->sum('duration') / 60; // Convert seconds to minutes
    $partsMinutes = round($partsMinutes, 1); // Round to 1 decimal place
    
    // Count calls by department
    $salesCalls = \App\Models\CallLog::where('dealership_id', $dealershipId)
        ->where('department', 'sales')
        ->count();
    
    $serviceCalls = \App\Models\CallLog::where('dealership_id', $dealershipId)
        ->where('department', 'service')
        ->count();
    
    $partsCalls = \App\Models\CallLog::where('dealership_id', $dealershipId)
        ->where('department', 'parts')
        ->count();
    
    $otherCalls = $totalCalls - $salesCalls - $serviceCalls - $partsCalls;
    
    return Inertia::render('Dashboard', [
        'callLogs' => $callLogs,
        'filters' => [
            'department' => $department,
        ],
        'stats' => [
            'totalCalls' => $totalCalls,
            'completedCalls' => $completedCalls,
            'percentAnswered' => $percentAnswered,
            'totalMinutes' => $totalMinutes,
            'salesCalls' => $salesCalls,
            'serviceCalls' => $serviceCalls,
            'partsCalls' => $partsCalls,
            'otherCalls' => $otherCalls,
            'salesMinutes' => $salesMinutes,
            'serviceMinutes' => $serviceMinutes,
            'partsMinutes' => $partsMinutes,
        ]
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Call Logs Routes
    Route::get('/call-logs', [CallLogsController::class, 'index'])->name('call-logs.index');
    Route::get('/call-logs/{id}', [CallLogsController::class, 'show'])->name('call-logs.show');
    Route::get('/api/dashboard/call-logs', [CallLogsController::class, 'forDashboard'])->name('api.dashboard.call-logs');
    
    // Knowledge Base Routes
    Route::get('/knowledge-bases', [KnowledgeBaseController::class, 'index'])->name('knowledge-bases.index');
    Route::post('/knowledge-bases', [KnowledgeBaseController::class, 'store'])->name('knowledge-bases.store');
    Route::get('/knowledge-bases/{id}', [KnowledgeBaseController::class, 'show'])->name('knowledge-bases.show');
    Route::get('/knowledge-bases/{id}/download', [KnowledgeBaseController::class, 'downloadFile'])->name('knowledge-bases.download');
});

require __DIR__.'/auth.php';
