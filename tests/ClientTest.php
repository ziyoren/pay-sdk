<?php

namespace Ziyoren\PaySdk\Tests;

use PHPUnit\Framework\TestCase;
use Ziyoren\PaySdk\Client;
use Ziyoren\PaySdk\Exceptions\ApiException;

class ClientTest extends TestCase
{
    private string $apiKey = 'test-api-key';
    private string $secretKey = 'test-secret-key';

    public function testCreateClientInstance(): void
    {
        $client = new Client($this->apiKey, $this->secretKey);

        $this->assertInstanceOf(Client::class, $client);
    }

    public function testPrepareRequestIncludesRequiredFields(): void
    {
        $client = new Client($this->apiKey, $this->secretKey);

        $reflection = new \ReflectionClass($client);
        $method = $reflection->getMethod('prepareRequest');
        $method->setAccessible(true);

        $orderData = ['amount' => 100];
        $action = 'create-payment-order';

        $result = $method->invoke($client, $orderData, $action);

        $this->assertArrayHasKey('api_key', $result);
        $this->assertArrayHasKey('timestamp', $result);
        $this->assertArrayHasKey('nonce_str', $result);
        $this->assertArrayHasKey('action', $result);
        $this->assertArrayHasKey('signature', $result);
        $this->assertEquals($this->apiKey, $result['api_key']);
        $this->assertEquals($action, $result['action']);
    }

    public function testVerifyNotificationReturnsBool(): void
    {
        $client = new Client($this->apiKey, $this->secretKey);

        // Create a valid signed payload
        $payload = [
            'order_id' => 'TEST-123',
            'amount' => 100,
            'signature' => hash_hmac('sha256', 'amount=100&order_id=TEST-123', $this->secretKey)
        ];

        $result = $client->verifyNotification($payload);

        $this->assertTrue($result);

        // Test invalid signature
        $payload['signature'] = 'invalid-signature';
        $result = $client->verifyNotification($payload);

        $this->assertFalse($result);
    }
}