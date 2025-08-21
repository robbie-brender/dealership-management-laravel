<?php

// Test script for VAPI webhook integration with department classification
// This script simulates VAPI webhook calls with different department information

// Function to make a POST request to the webhook endpoint
function sendWebhookRequest($data) {
    // First check if we have any call logs with the call_id before sending
    $callId = $data['message']['call_id'] ?? 'unknown';
    echo "Checking for existing call log with call_id: {$callId}\n";
    
    $url = 'http://localhost:8001/api/raw-webhook';
    $jsonData = json_encode($data);
    
    echo "Sending webhook request to: {$url}\n";
    echo "Request payload (first 200 chars): " . substr($jsonData, 0, 200) . "...\n";
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($jsonData)
    ]);
    
    $response = curl_exec($ch);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $headers = substr($response, 0, $headerSize);
    $body = substr($response, $headerSize);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    echo "Response HTTP code: {$httpCode}\n";
    echo "Response headers: \n{$headers}\n";
    echo "Response body: {$body}\n";
    if ($error) {
        echo "Curl error: {$error}\n";
    }
    
    return [
        'code' => $httpCode,
        'response' => $body,
        'headers' => $headers,
        'error' => $error
    ];
}

// Generate a unique test run ID to track this batch of tests
$testRunId = 'test-run-' . time();
echo "Test run ID: {$testRunId}\n\n";

// Test cases for different departments
$testCases = [
    // Test case 1: Explicit sales department
    [
        'event' => 'end-of-call-report',
        'message' => [
            'call_id' => $testRunId . '-1',
            'caller_number' => '+15551234001',
            'recipient_number' => '+15559876001',
            'department' => 'sales', // Explicit department field
            'call_classification' => 'SALES', // Added call_classification field
            'call_started_at' => date('Y-m-d H:i:s'),
            'duration' => 125,
            'summary' => 'Customer inquired about purchasing a new Toyota Camry and discussed financing options.',
            'transcript' => 'Customer: I\'m interested in buying a new car.\nAgent: Great! What model are you interested in?\nCustomer: The Toyota Camry.\nAgent: Excellent choice! Would you like to schedule a test drive?',
            'recording_url' => 'https://example.com/recordings/sales-call-1.mp3',
            'success' => true,
            'frustration' => 'low'
        ]
    ],
    
    // Test case 2: Explicit service department
    [
        'event' => 'end-of-call-report',
        'message' => [
            'call_id' => $testRunId . '-2',
            'caller_number' => '+15551234002',
            'recipient_number' => '+15559876002',
            'department' => 'service', // Explicit department field
            'call_classification' => 'SERVICE', // Added call_classification field
            'call_started_at' => date('Y-m-d H:i:s'),
            'duration' => 180,
            'summary' => 'Customer scheduled a maintenance appointment for oil change and tire rotation.',
            'transcript' => 'Customer: I need to bring my car in for service.\nAgent: What type of service do you need?\nCustomer: Oil change and tire rotation.\nAgent: We can schedule that for you. When would you like to come in?',
            'recording_url' => 'https://example.com/recordings/service-call-1.mp3',
            'success' => true,
            'frustration' => 'low'
        ]
    ],
    
    // Test case 3: Explicit parts department
    [
        'event' => 'end-of-call-report',
        'message' => [
            'call_id' => $testRunId . '-3',
            'caller_number' => '+15551234003',
            'recipient_number' => '+15559876003',
            'department' => 'parts', // Explicit department field
            'call_classification' => 'PARTS', // Added call_classification field
            'call_started_at' => date('Y-m-d H:i:s'),
            'duration' => 95,
            'summary' => 'Customer inquired about availability and pricing for replacement windshield wipers.',
            'transcript' => 'Customer: Do you have windshield wipers for a 2018 Toyota Corolla?\nAgent: Yes, we do have those in stock. Would you like to purchase them?\nCustomer: How much do they cost?\nAgent: They are $24.99 for the pair.',
            'recording_url' => 'https://example.com/recordings/parts-call-1.mp3',
            'success' => true,
            'frustration' => 'low'
        ]
    ],
    
    // Test case 4: Inferred sales department from summary/transcript
    [
        'event' => 'end-of-call-report',
        'message' => [
            'call_id' => $testRunId . '-4',
            'caller_number' => '+15551234004',
            'recipient_number' => '+15559876004',
            // No explicit department field
            'call_classification' => 'SALES', // Added call_classification field
            'call_started_at' => date('Y-m-d H:i:s'),
            'duration' => 210,
            'summary' => 'Customer was interested in purchasing a new SUV and discussed various models and pricing options.',
            'transcript' => 'Customer: I\'m looking for information on your SUV models.\nAgent: We have several SUVs available. Are you looking for a specific size?\nCustomer: Something mid-size with good fuel economy.\nAgent: I would recommend the RAV4 or the Highlander Hybrid.',
            'recording_url' => 'https://example.com/recordings/inferred-sales-call.mp3',
            'success' => true,
            'frustration' => 'low'
        ]
    ],
    
    // Test case 5: Inferred service department from summary/transcript
    [
        'event' => 'end-of-call-report',
        'message' => [
            'call_id' => $testRunId . '-5',
            'caller_number' => '+15551234005',
            'recipient_number' => '+15559876005',
            // No explicit department field
            'call_classification' => 'SERVICE', // Added call_classification field
            'call_started_at' => date('Y-m-d H:i:s'),
            'duration' => 145,
            'summary' => 'Customer reported a check engine light and scheduled a diagnostic appointment.',
            'transcript' => 'Customer: My check engine light came on yesterday.\nAgent: I\'m sorry to hear that. We should get that looked at right away.\nCustomer: When can I bring it in?\nAgent: We have an opening tomorrow at 10 AM for diagnostics.',
            'recording_url' => 'https://example.com/recordings/inferred-service-call.mp3',
            'success' => true,
            'frustration' => 'medium'
        ]
    ],
    
    // Test case 6: Inferred parts department from summary/transcript
    [
        'event' => 'end-of-call-report',
        'message' => [
            'call_id' => $testRunId . '-6',
            'caller_number' => '+15551234006',
            'recipient_number' => '+15559876006',
            // No explicit department field
            'call_classification' => 'PARTS', // Added call_classification field
            'call_started_at' => date('Y-m-d H:i:s'),
            'duration' => 85,
            'summary' => 'Customer inquired about availability of replacement floor mats and air filters.',
            'transcript' => 'Customer: Do you have floor mats for a 2020 Camry?\nAgent: Yes, we do. Would you like the all-weather or carpet mats?\nCustomer: The all-weather ones. And do you also have cabin air filters?\nAgent: Yes, we have those in stock as well.',
            'recording_url' => 'https://example.com/recordings/inferred-parts-call.mp3',
            'success' => true,
            'frustration' => 'low'
        ]
    ]
];

// Run the tests
echo "Starting VAPI webhook tests with department classification...\n\n";

foreach ($testCases as $index => $testCase) {
    $caseNum = $index + 1;
    echo "Test Case {$caseNum}: ";
    
    if (isset($testCase['message']['department'])) {
        echo "Explicit {$testCase['message']['department']} department\n";
    } else {
        echo "Inferred department from content\n";
    }
    
    $result = sendWebhookRequest($testCase);
    
    echo "Response Code: {$result['code']}\n";
    echo "Response: {$result['response']}\n";
    
    if (!empty($result['error'])) {
        echo "Error: {$result['error']}\n";
    }
    echo "\n";
    
    // Add a small delay between requests
    sleep(1);
}

// After all tests, wait a moment for async processing to complete
sleep(3);

// Check the database for our test call logs using Laravel's artisan tinker
echo "\nChecking database for call logs from this test run (ID: {$testRunId})...\n";

// Create a PHP script to run with artisan tinker
$checkScript = __DIR__ . '/temp_check_script.php';
file_put_contents($checkScript, "<?php\n\nuse App\\Models\\CallLog;\n\n\$testRunId = '{$testRunId}';\n\n// Get the call IDs we used in this test run\n\$callIds = [];\nfor (\$i = 1; \$i <= 6; \$i++) {\n    \$callIds[] = \$testRunId . '-' . \$i;\n}\n\n\$logs = CallLog::whereIn('call_id', \$callIds)\n    ->orderBy('id', 'desc')\n    ->get(['id', 'call_id', 'department', 'created_at']);\n\nif (\$logs->count() > 0) {\n    echo \"Found \" . \$logs->count() . \" call logs from this test run:\n\";\n    foreach (\$logs as \$log) {\n        echo \"ID: {\$log->id}, Call ID: {\$log->call_id}, Department: {\$log->department}, Created: {\$log->created_at}\n\";\n    }\n} else {\n    echo \"No call logs found from this test run.\n\";\n}\n");

// Execute the check script with artisan tinker
$command = "cd " . __DIR__ . "/../ && php artisan tinker --execute=\"require '{$checkScript}';\"";
$output = shell_exec($command);
echo $output;

// Clean up the temporary script
unlink($checkScript);

echo "\nAll tests completed.\n";
