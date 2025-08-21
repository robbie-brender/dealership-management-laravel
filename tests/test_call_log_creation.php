<?php

// Test script to directly create a call log with department information
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CallLog;
use Illuminate\Support\Facades\DB;

echo "Testing direct call log creation with department...\n";

// First, check if the call_logs table exists
try {
    $tableExists = Schema::hasTable('call_logs');
    echo "Call logs table exists: " . ($tableExists ? 'Yes' : 'No') . "\n";
    
    if ($tableExists) {
        // Check table structure
        $columns = Schema::getColumnListing('call_logs');
        echo "Table columns: " . implode(', ', $columns) . "\n";
        
        // Check if department column exists
        $hasDepartmentColumn = in_array('department', $columns);
        echo "Has department column: " . ($hasDepartmentColumn ? 'Yes' : 'No') . "\n";
        
        // Create a test call log with department
        $callLog = new CallLog();
        $callLog->call_id = 'test-direct-' . time();
        $callLog->caller_number = '+15551234099';
        $callLog->recipient_number = '+15559876099';
        $callLog->department = 'SALES';
        $callLog->call_started_at = now();
        $callLog->duration = 120;
        $callLog->vapi_summary = ['summary' => 'Test direct creation with department'];
        $callLog->transcript = 'This is a test transcript';
        $callLog->recording_url = 'https://example.com/test-recording.mp3';
        $callLog->save();
        
        echo "Created call log with ID: " . $callLog->id . "\n";
        
        // Verify the call log was created with department
        $savedLog = CallLog::find($callLog->id);
        echo "Retrieved call log department: " . $savedLog->department . "\n";
        
        // Show the most recent call logs
        $recentLogs = CallLog::select('id', 'call_id', 'department')
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();
            
        echo "Recent call logs:\n";
        foreach ($recentLogs as $log) {
            echo "ID: {$log->id}, Call ID: {$log->call_id}, Department: {$log->department}\n";
        }
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "Test completed.\n";
