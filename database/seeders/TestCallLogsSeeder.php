<?php

namespace Database\Seeders;

use App\Models\CallLog;
use Illuminate\Database\Seeder;

class TestCallLogsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sales department call
        CallLog::create([
            'call_id' => 'test-sales-call-001',
            'caller_number' => '+15551234001',
            'recipient_number' => '+15559876001',
            'direction' => 'inbound',
            'status' => 'completed',
            'call_started_at' => now()->subHours(2),
            'call_ended_at' => now()->subHours(2)->addMinutes(5),
            'duration' => 300,
            'department' => 'sales',
            'metadata' => json_encode([
                'summary' => 'Customer inquired about purchasing a new Toyota Camry and discussed financing options.',
                'transcript' => 'Customer: I\'m interested in buying a new car.\nAgent: Great! What model are you interested in?\nCustomer: The Toyota Camry.',
                'recording_url' => 'https://example.com/recordings/sales-call-1.mp3',
                'success' => true,
                'frustration' => 'low'
            ])
        ]);

        // Service department call
        CallLog::create([
            'call_id' => 'test-service-call-001',
            'caller_number' => '+15551234002',
            'recipient_number' => '+15559876002',
            'direction' => 'inbound',
            'status' => 'completed',
            'call_started_at' => now()->subHours(1),
            'call_ended_at' => now()->subHours(1)->addMinutes(8),
            'duration' => 480,
            'department' => 'service',
            'metadata' => json_encode([
                'summary' => 'Customer scheduled a maintenance appointment for oil change and tire rotation.',
                'transcript' => 'Customer: I need to bring my car in for service.\nAgent: What type of service do you need?\nCustomer: Oil change and tire rotation.',
                'recording_url' => 'https://example.com/recordings/service-call-1.mp3',
                'success' => true,
                'frustration' => 'low'
            ])
        ]);

        // Parts department call
        CallLog::create([
            'call_id' => 'test-parts-call-001',
            'caller_number' => '+15551234003',
            'recipient_number' => '+15559876003',
            'direction' => 'inbound',
            'status' => 'completed',
            'call_started_at' => now()->subMinutes(30),
            'call_ended_at' => now()->subMinutes(30)->addMinutes(4),
            'duration' => 240,
            'department' => 'parts',
            'metadata' => json_encode([
                'summary' => 'Customer inquired about availability and pricing for replacement windshield wipers.',
                'transcript' => 'Customer: Do you have windshield wipers for a 2018 Toyota Corolla?\nAgent: Yes, we do have those in stock.',
                'recording_url' => 'https://example.com/recordings/parts-call-1.mp3',
                'success' => true,
                'frustration' => 'low'
            ])
        ]);

        // Call with no department (should show dash in UI)
        CallLog::create([
            'call_id' => 'test-general-call-001',
            'caller_number' => '+15551234004',
            'recipient_number' => '+15559876004',
            'direction' => 'inbound',
            'status' => 'completed',
            'call_started_at' => now()->subMinutes(15),
            'call_ended_at' => now()->subMinutes(15)->addMinutes(3),
            'duration' => 180,
            'department' => null,
            'metadata' => json_encode([
                'summary' => 'General inquiry about dealership hours and location.',
                'transcript' => 'Customer: What are your hours today?\nAgent: We\'re open from 9 AM to 7 PM today.',
                'recording_url' => 'https://example.com/recordings/general-call-1.mp3',
                'success' => true,
                'frustration' => 'low'
            ])
        ]);
    }
}
