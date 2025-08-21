<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| Raw Webhook Routes
|--------------------------------------------------------------------------
|
| These routes are registered directly with no middleware whatsoever.
| They are specifically designed for external webhook services that
| need to send POST requests without any CSRF protection.
|
*/

// Raw webhook endpoint with absolutely no middleware
Route::post('/webhook', function(Request $request) {
    Log::info('Raw webhook received at /webhook', [
        'data' => $request->all(),
        'headers' => $request->headers->all(),
        'path' => $request->path(),
        'url' => $request->url(),
        'method' => $request->method()
    ]);
    
    return response()->json([
        'status' => 'success',
        'message' => 'Raw webhook received',
        'data' => $request->all()
    ]);
});

// Root URL webhook endpoint
Route::post('/', function(Request $request) {
    Log::info('Raw webhook received at root URL', [
        'data' => $request->all(),
        'headers' => $request->headers->all(),
        'path' => $request->path(),
        'url' => $request->url(),
        'method' => $request->method()
    ]);
    
    return response()->json([
        'status' => 'success',
        'message' => 'Raw webhook received at root URL',
        'data' => $request->all()
    ]);
});

// VAPI webhook endpoint
Route::post('/webhooks/vapi', function(Request $request) {
    Log::info('Raw VAPI webhook received', [
        'data' => $request->all(),
        'headers' => $request->headers->all(),
        'path' => $request->path(),
        'url' => $request->url(),
        'method' => $request->method()
    ]);
    
    return response()->json([
        'status' => 'success',
        'message' => 'Raw VAPI webhook received',
        'data' => $request->all()
    ]);
});

// VAPI webhook endpoint at /api/raw-webhook
Route::post('/api/raw-webhook', function(Request $request) {
    Log::info('VAPI webhook received at /api/raw-webhook', [
        'data' => $request->all(),
        'headers' => $request->headers->all(),
        'path' => $request->path(),
        'url' => $request->url(),
        'method' => $request->method()
    ]);
    
    return response()->json([
        'status' => 'success',
        'message' => 'VAPI webhook received',
        'data' => $request->all()
    ]);
});
