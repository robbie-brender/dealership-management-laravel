<?php

use App\Http\Controllers\NgrokWebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Ngrok Webhook Routes
|--------------------------------------------------------------------------
|
| These routes are specifically for handling webhook requests coming through
| the ngrok tunnel. They are configured to bypass CSRF protection and other
| middleware that might interfere with webhook processing.
|
*/

// Root URL webhook endpoint
Route::post('/', [NgrokWebhookController::class, 'handleWebhook']);

// Standard webhook endpoints
Route::post('/webhook', [NgrokWebhookController::class, 'handleWebhook']);
Route::post('/webhooks/vapi', [NgrokWebhookController::class, 'handleWebhook']);

// Catch-all route for any other webhook paths
Route::post('/{any}', [NgrokWebhookController::class, 'handleWebhook'])->where('any', '.*');
