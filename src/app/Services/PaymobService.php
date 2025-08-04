<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PaymobService
{
  protected $apiKey;
  protected $integrationId;
  protected $iframeId;
  protected $baseUrl;
  public function __construct()
  {
    $this->baseUrl = config('paymob.base_url');
    $this->apiKey = config('paymob.api_key');
    $this->integrationId = config('paymob.integration_id');
    $this->iframeId = config('paymob.iframe_id');
  }

  // Step 1: Authentication
  public function getAuthToken()
  {
    $response = Http::post('https://accept.paymob.com/api/auth/tokens', [
      'api_key' => $this->apiKey
    ]);

    $token = $response['token'];
    return $token;
  }

  // Step 2: Create Order
  public function createOrder($authToken, $amount, $metadata = [])
  {
    $response = Http::withToken($authToken)->post('https://accept.paymob.com/api/ecommerce/orders', [
      'delivery_needed' => false,
      'amount_cents' => $amount * 100,
      'items' => [],
      'merchant_order_id' => base64_encode(json_encode($metadata)) . '.' . uniqid(),
      'metadata' => $metadata
    ]);

    if (!$response->successful()) {
      throw new \Exception('Create Order Failed: ' . $response->body());
    }

    if (!isset($response['id'])) {
      throw new \Exception('Order ID not found in response: ' . $response->body());
    }

    return $response['id'];
  }

  // Step 3: Generate Payment Key
  public function generatePaymentKey($authToken, $amount, $orderId, $email, $firstName)
  {
    $response = Http::withToken($authToken)->post('https://accept.paymob.com/api/acceptance/payment_keys', [
      'amount_cents' => $amount * 100,
      'expiration' => 3600,
      'order_id' => $orderId,
      'billing_data' => [
        "apartment" => "NA",
        "email" => $email,
        "floor" => "NA",
        "first_name" => $firstName,
        "last_name" => "NA",
        "street" => "NA",
        "building" => "NA",
        "phone_number" => "01000000000",
        "shipping_method" => "NA",
        "postal_code" => "NA",
        "city" => "Cairo",
        "country" => "EG",
        "state" => "NA"
      ],
      'currency' => 'EGP',
      'integration_id' => $this->integrationId

    ]);

    return $response['token'];
  }

  // Step 4: Generate IFrame URL
  public function generateIframeUrl($paymentToken)
  {
    return "https://accept.paymob.com/api/acceptance/iframes/{$this->iframeId}?payment_token={$paymentToken}";
  }

  // Full Payment Flow
  public function getPaymentUrl($amount, $email, $firstName = 'Test', $metadata = [])
  {
    $authToken = $this->getAuthToken();
    $orderId = $this->createOrder($authToken, $amount, $metadata);
    $paymentKey = $this->generatePaymentKey($authToken, $amount, $orderId, $email, $firstName);
    return $this->generateIframeUrl($paymentKey);
  }
}
