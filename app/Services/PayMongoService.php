<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayMongoService
{
    private $secretKey;
    private $publicKey;
    private $baseUrl = 'https://api.paymongo.com/v1';

    public function __construct()
    {
        $this->secretKey = config('services.paymongo.secret_key');
        $this->publicKey = config('services.paymongo.public_key');
    }

    /**
     * Create a Payment Intent
     */
    public function createPaymentIntent($amount, $description, $metadata = [])
    {
        $response = Http::withBasicAuth($this->secretKey, '')
            ->post("{$this->baseUrl}/payment_intents", [
                'data' => [
                    'attributes' => [
                        'amount' => $amount * 100, // Convert to centavos
                        'payment_method_allowed' => ['card', 'paymaya', 'gcash'],
                        'payment_method_options' => [
                            'card' => ['request_three_d_secure' => 'automatic']
                        ],
                        'currency' => 'PHP',
                        'description' => $description,
                        'statement_descriptor' => 'Your Store',
                        'metadata' => $metadata
                    ]
                ]
            ]);

        return $response->json();
    }

    /**
     * Attach Payment Method to Payment Intent
     */
    public function attachPaymentMethod($paymentIntentId, $paymentMethodId)
    {
        $response = Http::withBasicAuth($this->secretKey, '')
            ->post("{$this->baseUrl}/payment_intents/{$paymentIntentId}/attach", [
                'data' => [
                    'attributes' => [
                        'payment_method' => $paymentMethodId
                    ]
                ]
            ]);

        return $response->json();
    }

    /**
     * Retrieve Payment Intent
     */
    public function getPaymentIntent($paymentIntentId)
    {
        $response = Http::withBasicAuth($this->secretKey, '')
            ->get("{$this->baseUrl}/payment_intents/{$paymentIntentId}");

        return $response->json();
    }

    /**
     * Create a Payment Method (Card)
     */
    public function createPaymentMethod($cardDetails)
    {
        $response = Http::withBasicAuth($this->publicKey, '')
            ->post("{$this->baseUrl}/payment_methods", [
                'data' => [
                    'attributes' => [
                        'type' => 'card',
                        'details' => $cardDetails
                    ]
                ]
            ]);

        return $response->json();
    }

    /**
     * Create a Source (for GCash, GrabPay, etc.)
     */
    public function createSource($amount, $type, $redirectUrl)
    {
        $response = Http::withBasicAuth($this->secretKey, '')
            ->post("{$this->baseUrl}/sources", [
                'data' => [
                    'attributes' => [
                        'amount' => $amount * 100,
                        'redirect' => [
                            'success' => $redirectUrl['success'],
                            'failed' => $redirectUrl['failed']
                        ],
                        'type' => $type, // 'gcash' or 'grab_pay'
                        'currency' => 'PHP'
                    ]
                ]
            ]);

        return $response->json();
    }

    /**
     * Create Webhook
     */
    public function createWebhook($url, $events)
    {
        $response = Http::withBasicAuth($this->secretKey, '')
            ->post("{$this->baseUrl}/webhooks", [
                'data' => [
                    'attributes' => [
                        'url' => $url,
                        'events' => $events
                    ]
                ]
            ]);

        return $response->json();
    }

    /**
     * List Webhooks
     */
    public function listWebhooks()
    {
        $response = Http::withBasicAuth($this->secretKey, '')
            ->get("{$this->baseUrl}/webhooks");

        return $response->json();
    }

    /**
     * Verify Webhook Signature
     */
    public function verifyWebhookSignature($payload, $signature)
    {
        $webhookSecret = config('services.paymongo.webhook_secret');
        $computedSignature = hash_hmac('sha256', $payload, $webhookSecret);
        
        return hash_equals($computedSignature, $signature);
    }
}