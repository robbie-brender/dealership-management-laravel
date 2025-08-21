<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\RequestException;

class VapiService
{
    /**
     * The Vapi API base URL.
     *
     * @var string
     */
    protected $baseUrl;

    /**
     * The Vapi API key.
     *
     * @var string
     */
    protected $apiKey;

    /**
     * The Vapi API version.
     *
     * @var string
     */
    protected $version;

    /**
     * Create a new Vapi service instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->baseUrl = config('vapi.base_url');
        $this->apiKey = config('vapi.api_key');
        $this->version = config('vapi.version');
    }

    /**
     * Get the base URL for API requests.
     *
     * @return string
     */
    protected function getBaseUrl()
    {
        return "{$this->baseUrl}/{$this->version}";
    }

    /**
     * Get the HTTP client instance with authentication headers.
     *
     * @return \Illuminate\Http\Client\PendingRequest
     */
    protected function http()
    {
        return Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ]);
    }

    /**
     * Get all call logs with optional filters.
     *
     * @param array $filters
     * @return array|null
     */
    public function getCallLogs(array $filters = [])
    {
        try {
            $response = $this->http()->get($this->getBaseUrl() . '/calls', $filters);
            
            return $response->successful() ? $response->json() : null;
        } catch (RequestException $e) {
            Log::error('Vapi API Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get a specific call by ID.
     *
     * @param string $callId
     * @return array|null
     */
    public function getCall(string $callId)
    {
        try {
            $response = $this->http()->get($this->getBaseUrl() . "/calls/{$callId}");
            
            return $response->successful() ? $response->json() : null;
        } catch (RequestException $e) {
            Log::error('Vapi API Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Create a new outbound call.
     *
     * @param array $callData
     * @return array|null
     */
    public function createCall(array $callData)
    {
        try {
            $response = $this->http()->post($this->getBaseUrl() . '/calls', $callData);
            
            return $response->successful() ? $response->json() : null;
        } catch (RequestException $e) {
            Log::error('Vapi API Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get all assistants.
     *
     * @return array|null
     */
    public function getAssistants()
    {
        try {
            $response = $this->http()->get($this->getBaseUrl() . '/assistants');
            
            return $response->successful() ? $response->json() : null;
        } catch (RequestException $e) {
            Log::error('Vapi API Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get a specific assistant by ID.
     *
     * @param string $assistantId
     * @return array|null
     */
    public function getAssistant(string $assistantId)
    {
        try {
            $response = $this->http()->get($this->getBaseUrl() . "/assistants/{$assistantId}");
            
            return $response->successful() ? $response->json() : null;
        } catch (RequestException $e) {
            Log::error('Vapi API Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Verify webhook signature.
     *
     * @param string $signature
     * @param string $payload
     * @return bool
     */
    public function verifyWebhookSignature(string $signature, string $payload)
    {
        $secret = config('vapi.webhook_secret');
        
        if (empty($secret)) {
            return false;
        }
        
        $computedSignature = hash_hmac('sha256', $payload, $secret);
        
        return hash_equals($computedSignature, $signature);
    }
}
