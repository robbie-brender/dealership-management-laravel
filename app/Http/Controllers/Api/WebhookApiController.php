<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookApiController extends Controller
{
    /**
     * Handle the incoming webhook request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function handleWebhook(Request $request)
    {
        // Log the webhook data
        Log::info('API Webhook received', [
            'data' => $request->all(),
            'headers' => $request->headers->all(),
            'path' => $request->path(),
            'url' => $request->url(),
            'method' => $request->method()
        ]);

        // Process the webhook data
        // Add your webhook processing logic here

        // Return a JSON response
        return response()->json([
            'status' => 'success',
            'message' => 'Webhook received via API endpoint',
            'data' => $request->all()
        ]);
    }
}
