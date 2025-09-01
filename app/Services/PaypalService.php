<?php

namespace App\Services;

use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Exception;

class PayPalService
{
    /** @var PayPalClient */
    private $paypalClient;

    public function __construct()
    {
        $this->paypalClient = new PayPalClient;
        $this->paypalClient->setApiCredentials(config('paypal'));
        $this->paypalClient->setAccessToken($this->paypalClient->getAccessToken());
    }

    /**
     * Create a PayPal payment
     * 
     * @param array $orderData Order data containing cart items and shipping
     * @return array PayPal order response
     * @throws Exception
     */
    public function createPayment(array $orderData): array
    {
        $items = [];
        $total = 0;

        // Create PayPal items from cart
        foreach ($orderData['cart'] as $item) {
            $price = isset($item['discount_price']) && $item['discount_price'] < $item['price'] 
                     ? $item['discount_price'] 
                     : $item['price'];

            $items[] = [
                'name' => $item['name'],
                'quantity' => (string) $item['quantity'],
                'unit_amount' => [
                    'currency_code' => 'PHP',
                    'value' => number_format($price, 2, '.', '')
                ],
                'category' => 'PHYSICAL_GOODS'
            ];

            $total += $price * $item['quantity'];
        }

        // Add shipping if applicable
        if (isset($orderData['shipping_cost']) && $orderData['shipping_cost'] > 0) {
            $items[] = [
                'name' => 'Shipping',
                'quantity' => '1',
                'unit_amount' => [
                    'currency_code' => 'PHP',
                    'value' => number_format($orderData['shipping_cost'], 2, '.', '')
                ],
                'category' => 'PHYSICAL_GOODS'
            ];
            $total += $orderData['shipping_cost'];
        }

        $paypalOrder = [
            'intent' => 'CAPTURE',
            'application_context' => [
                'return_url' => route('paypal.success'),
                'cancel_url' => route('paypal.cancel'),
                'brand_name' => config('app.name', 'Your Store'),
                'locale' => 'en-PH',
                'landing_page' => 'BILLING',
                'shipping_preference' => 'SET_PROVIDED_ADDRESS',
                'user_action' => 'PAY_NOW'
            ],
            'purchase_units' => [
                [
                    'reference_id' => uniqid('ORDER_'),
                    'description' => 'Order from Your Store',
                    'amount' => [
                        'currency_code' => 'PHP',
                        'value' => number_format($total, 2, '.', ''),
                        'breakdown' => [
                            'item_total' => [
                                'currency_code' => 'PHP',
                                'value' => number_format($total, 2, '.', '')
                            ]
                        ]
                    ],
                    'items' => $items,
                    'shipping' => $this->formatShippingAddress($orderData)
                ]
            ]
        ];

        try {
            $response = $this->paypalClient->createOrder($paypalOrder);
            
            if (isset($response['status']) && $response['status'] === 'CREATED') {
                return $response;
            }
            
            throw new Exception('PayPal order creation failed: ' . json_encode($response));
        } catch (Exception $ex) {
            throw new Exception('PayPal payment creation failed: ' . $ex->getMessage());
        }
    }

    /**
     * Capture/Execute a PayPal payment
     * 
     * @param string $orderId PayPal order ID
     * @return array PayPal capture response
     * @throws Exception
     */
    public function capturePayment(string $orderId): array
    {
        try {
            $response = $this->paypalClient->capturePaymentOrder($orderId);
            
            if (isset($response['status']) && $response['status'] === 'COMPLETED') {
                return $response;
            }
            
            throw new Exception('PayPal payment capture failed: ' . json_encode($response));
        } catch (Exception $ex) {
            throw new Exception('PayPal payment execution failed: ' . $ex->getMessage());
        }
    }

    /**
     * Get PayPal order details
     * 
     * @param string $orderId
     * @return array
     * @throws Exception
     */
    public function getOrderDetails(string $orderId): array
    {
        try {
            return $this->paypalClient->showOrderDetails($orderId);
        } catch (Exception $ex) {
            throw new Exception('Failed to get PayPal order details: ' . $ex->getMessage());
        }
    }

    /**
     * Format shipping address for PayPal
     * 
     * @param array $orderData
     * @return array|null
     */
    private function formatShippingAddress(array $orderData): ?array
    {
        if (!isset($orderData['shipping_address'])) {
            return null;
        }

        $address = $orderData['shipping_address'];
        
        return [
            'name' => [
                'full_name' => $address['full_name'] ?? $address['first_name'] . ' ' . $address['last_name']
            ],
            'address' => [
                'address_line_1' => $address['address_line_1'],
                'address_line_2' => $address['address_line_2'] ?? '',
                'admin_area_2' => $address['city'],
                'admin_area_1' => $address['state'] ?? $address['province'],
                'postal_code' => $address['postal_code'],
                'country_code' => $address['country_code'] ?? 'PH'
            ]
        ];
    }
}