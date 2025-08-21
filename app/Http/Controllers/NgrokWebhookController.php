<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NgrokWebhookController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Explicitly disable CSRF protection for this controller
        $this->middleware('web')->except(['handleWebhook']);
    }

    /**
     * Handle the incoming webhook request from ngrok.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function handleWebhook(Request $request)
    {
        // Log the webhook data
        Log::info('Ngrok webhook received', [
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
            'message' => 'Webhook received via ngrok',
            'data' => $request->all()
        ]);
    }
}
