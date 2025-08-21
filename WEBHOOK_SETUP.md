# Webhook Integration Setup

This document outlines how webhook requests are handled in the application, particularly when using ngrok for external access.

## Solution Overview

We've implemented multiple approaches to handle webhook requests without CSRF token issues:

1. **Raw Webhook Endpoint**: A dedicated endpoint with no middleware at `/api/raw-webhook`
2. **API Webhook Controller**: A specialized controller for handling webhook requests
3. **TrustProxies Configuration**: Modified to trust all proxies, including ngrok

## Implementation Details

### 1. Raw Webhook Endpoint

The most reliable solution is using the raw webhook endpoint in `routes/api.php`:

```php
// Raw webhook endpoint with absolutely no middleware
Route::post('/raw-webhook', function(\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Log::info('Raw webhook received', ['data' => $request->all()]);
    return response()->json(['status' => 'success', 'message' => 'Raw webhook received', 'data' => $request->all()]);
})->middleware([]);
```

This endpoint:
- Uses an empty middleware array to bypass all middleware including CSRF protection
- Logs incoming webhook data
- Returns a JSON response

### 2. API Webhook Controller

We created a dedicated controller at `app/Http/Controllers/Api/WebhookApiController.php` for handling webhook requests with detailed logging.

### 3. TrustProxies Configuration

Modified `app/Http/Middleware/TrustProxies.php` to trust all proxies:

```php
protected $proxies = '*'; // Trust all proxies, including ngrok
```

## Testing Webhooks

To test webhook functionality:

```bash
# Test the raw webhook endpoint
curl -X POST https://[your-ngrok-url]/api/raw-webhook -d '{"test":"data"}' -H "Content-Type: application/json"
```

## Important URL Format Note

Routes defined in `routes/api.php` are automatically prefixed with `/api/`. This means:

- A route defined as `/raw-webhook` in `routes/api.php` is accessible at `/api/raw-webhook`
- When configuring external services to send webhooks, use the full URL with the `/api/` prefix

## Environment Configuration

Added to `.env`:

```
NGROK_URL=https://14a6b082c528.ngrok-free.app
```

## Troubleshooting

If webhook requests still fail:

1. Check Laravel logs: `storage/logs/laravel.log`
2. Verify ngrok is properly forwarding requests
3. Ensure the webhook URL in external services includes the `/api/` prefix
