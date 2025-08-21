<?php

use App\Http\Controllers\API\VapiWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Webhook routes for ngrok - with explicit middleware removal
Route::post('/webhook', [\App\Http\Controllers\Api\WebhookApiController::class, 'handleWebhook'])->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
Route::post('/webhooks/vapi', [\App\Http\Controllers\Api\WebhookApiController::class, 'handleWebhook'])->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
Route::post('/', [\App\Http\Controllers\Api\WebhookApiController::class, 'handleWebhook'])->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

// Raw webhook endpoint with absolutely no middleware
Route::post('/raw-webhook', [\App\Http\Controllers\Api\VapiWebhookController::class, 'handleWebhook'])->middleware([]);
