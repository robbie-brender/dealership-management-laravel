<?php

// Bootstrap Laravel application
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CallLog;
use Illuminate\Support\Facades\DB;

echo "Testing direct call log creation with department...\n";

// Generate unique test IDs
$testId = 'direct-test-' . time();

// Test data for different departments
$departments = ['sales', 'service', 'parts'];

foreach ($departments as $index => $department) {
    $callId = $testId . '-' . $department;
    
    try {
        // Create call log with department
        $callLog = CallLog::create([
            'call_id' => $callId,
            'status' => 'completed',
            'direction' => 'inbound',
            'caller_number' => '+15551234' . str_pad($index, 3, '0', STR_PAD_LEFT),
            'recipient_number' => '+15559876' . str_pad($index, 3, '0', STR_PAD_LEFT),
            'assistant_id' => 'test-assistant-' . $index,
            'transcript' => 'Test transcript for ' . $department . ' department',
            'recording_url' => 'https://example.com/recordings/' . $department . '-call.mp3',
            'duration' => 100 + $index * 10,
            'call_started_at' => date('Y-m-d H:i:s', strtotime('-1 hour')),
            'call_ended_at' => date('Y-m-d H:i:s'),
            'metadata' => json_encode(['test' => true, 'department' => $department]),
            'dealership_id' => 2,
            'department' => $department,
            'vapi_summary' => json_encode(['summary' => 'Test summary for ' . $department . ' department']),
            'vapi_success_evaluation' => true, // Boolean, not JSON
            'vapi_analysis' => json_encode(['analysis' => 'Test analysis for ' . $department . ' department']),
            'vapi_recording_url' => 'https://example.com/recordings/' . $department . '-call.mp3',
            'vapi_stereo_recording_url' => 'https://example.com/recordings/' . $department . '-call-stereo.mp3',
            'vapi_cost' => 0.50 + $index * 0.10,
            'vapi_duration_seconds' => 100 + $index * 10
        ]);
        
        echo "Successfully created call log for {$department} department with ID: {$callLog->id}\n";
    } catch (\Exception $e) {
        echo "Error creating call log for {$department} department: " . $e->getMessage() . "\n";
        echo "Stack trace: " . $e->getTraceAsString() . "\n";
    }
}

// Verify the created call logs
echo "\nVerifying created call logs...\n";
$callLogs = CallLog::where('call_id', 'like', $testId . '-%')
    ->orderBy('id', 'desc')
    ->get(['id', 'call_id', 'department', 'created_at']);

if ($callLogs->count() > 0) {
    echo "Found " . $callLogs->count() . " call logs:\n";
    foreach ($callLogs as $log) {
        echo "ID: {$log->id}, Call ID: {$log->call_id}, Department: {$log->department}, Created: {$log->created_at}\n";
    }
} else {
    echo "No call logs found with test ID: {$testId}\n";
}

echo "\nTest completed.\n";
