<?php

// Bootstrap Laravel application
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CallLog;
use Illuminate\Support\Facades\DB;

echo "Checking database for call logs with departments...\n";

try {
    // Query for recent call logs with department information
    $callLogs = CallLog::select('id', 'call_id', 'department', 'created_at')
        ->orderBy('id', 'desc')
        ->limit(10)
        ->get();
    
    if ($callLogs->count() > 0) {
        echo "Found " . $callLogs->count() . " call logs:\n";
        foreach ($callLogs as $log) {
            echo "ID: {$log->id}, Call ID: {$log->call_id}, Department: {$log->department}, Created: {$log->created_at}\n";
        }
    } else {
        echo "No call logs found in the database.\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "Check completed.\n";
