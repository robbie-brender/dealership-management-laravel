<?php

use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Webhook Routes
|--------------------------------------------------------------------------
|
| Here is where you can register webhook routes for your application.
| These routes are loaded by the RouteServiceProvider and have minimal
| middleware applied to them (no CSRF, no session).
|
*/

// Root URL webhook handler
Route::post('/', [WebhookController::class, 'handleWebhook']);

// Additional webhook endpoints
Route::post('/webhook', [WebhookController::class, 'handleWebhook']);
Route::post('/webhooks/vapi', [WebhookController::class, 'handleWebhook']);
