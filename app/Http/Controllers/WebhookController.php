<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Disable CSRF protection for this controller
        $this->middleware('web')->except(['handleWebhook']);
    }

    /**
     * Handle the incoming webhook request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function handleWebhook(Request $request)
    {
        // Log the webhook data
        Log::info('Webhook received', ['data' => $request->all(), 'headers' => $request->headers->all()]);

        // Process the webhook data
        // Add your webhook processing logic here

        // Return a response
        return response()->json(['status' => 'success', 'message' => 'Webhook received', 'data' => $request->all()]);
    }
}
