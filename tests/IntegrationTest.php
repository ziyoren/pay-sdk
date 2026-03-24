<?php

namespace Ziyoren\PaySdk\Tests;

use PHPUnit\Framework\TestCase;
use Ziyoren\PaySdk\Client;
use Ziyoren\PaySdk\SignatureGenerator;
use Ziyoren\PaySdk\SignatureVerifier;

class IntegrationTest extends TestCase
{
    public function testSdkComponentsIntegration(): void
    {
        // This test verifies that all SDK components work together
        $client = new Client('test-api-key', 'test-secret-key');

        $this->assertInstanceOf(Client::class, $client);

        // Test that the client internally uses the signature components
        $reflection = new \ReflectionClass($client);
        $generatorProperty = $reflection->getProperty('signatureGenerator');
        $generatorProperty->setAccessible(true);
        $verifierProperty = $reflection->getProperty('signatureVerifier');
        $verifierProperty->setAccessible(true);

        $signatureGenerator = $generatorProperty->getValue($client);
        $signatureVerifier = $verifierProperty->getValue($client);

        $this->assertInstanceOf(SignatureGenerator::class, $signatureGenerator);
        $this->assertInstanceOf(SignatureVerifier::class, $signatureVerifier);
    }

    public function testEndToEndSignatureFlow(): void
    {
        $secretKey = 'test-secret-key';
        $client = new Client('test-api-key', $secretKey);

        // Create test data
        $testData = [
            'param1' => 'value1',
            'param2' => 'value2',
            'timestamp' => time()
        ];

        // Simulate preparing a request (this calls signature generation internally)
        $reflection = new \ReflectionClass($client);
        $prepareRequestMethod = $reflection->getMethod('prepareRequest');
        $prepareRequestMethod->setAccessible(true);

        $request = $prepareRequestMethod->invoke($client, $testData, 'test-action');

        // Verify the request has the required fields including signature
        $this->assertArrayHasKey('signature', $request);
        $this->assertNotEmpty($request['signature']);
        $this->assertEquals('test-action', $request['action']);

        // Verify the signature can be validated
        $isValid = $client->verifyNotification($request);
        $this->assertTrue($isValid);
    }
}