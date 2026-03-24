<?php

namespace Ziyoren\PaySdk;

use Ziyoren\PaySdk\Exceptions\ApiException;
use Ziyoren\PaySdk\SignatureGenerator;
use Ziyoren\PaySdk\SignatureVerifier;

class Client
{
    private string $apiKey;
    private string $secretKey;
    private string $baseUrl;
    private SignatureGenerator $signatureGenerator;
    private SignatureVerifier $signatureVerifier;

    public function __construct(
        string $apiKey,
        string $secretKey,
        string $baseUrl = 'http://localhost:9898'
    ) {
        $this->apiKey = $apiKey;
        $this->secretKey = $secretKey;
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->signatureGenerator = new SignatureGenerator($secretKey);
        $this->signatureVerifier = new SignatureVerifier($secretKey);
    }

    public function createPaymentOrder(array $orderData): array
    {
        $requestParams = $this->prepareRequest($orderData, 'create-payment-order');

        return $this->makeApiCall('/api/payment/create', $requestParams);
    }

    public function getPaymentStatus(string $outTradeNo): array
    {
        $requestParams = $this->prepareRequest(['out_trade_no' => $outTradeNo], 'get-payment-status');

        return $this->makeApiCall('/api/payment/status', $requestParams);
    }

    public function verifyNotification(array $notificationData): bool
    {
        return $this->signatureVerifier->verify($notificationData);
    }

    private function prepareRequest(array $data, string $action): array
    {
        $request = array_merge([
            'api_key' => $this->apiKey,
            'timestamp' => time(),
            'nonce_str' => bin2hex(random_bytes(16)),
            'action' => $action
        ], $data);

        $request['signature'] = $this->signatureGenerator->generate($request);

        return $request;
    }

    private function makeApiCall(string $endpoint, array $params): array
    {
        $url = $this->baseUrl . $endpoint;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new ApiException("API request failed: {$error}");
        }

        if ($httpCode !== 200) {
            throw new ApiException("API request returned HTTP {$httpCode}: {$response}");
        }

        $result = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ApiException("Invalid JSON response: {$response}");
        }

        return $result;
    }
}