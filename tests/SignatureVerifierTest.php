<?php

namespace Ziyoren\PaySdk\Tests;

use PHPUnit\Framework\TestCase;
use Ziyoren\PaySdk\SignatureVerifier;

class SignatureVerifierTest extends TestCase
{
    private string $testSecret = 'test_secret_key';

    public function testVerifyValidSignature(): void
    {
        $verifier = new SignatureVerifier($this->testSecret);

        $params = [
            'param1' => 'value1',
            'param2' => 'value2',
            'signature' => hash_hmac('sha256', 'param1=value1&param2=value2', $this->testSecret)
        ];

        $result = $verifier->verify($params);

        $this->assertTrue($result);
    }

    public function testVerifyInvalidSignature(): void
    {
        $verifier = new SignatureVerifier($this->testSecret);

        $params = [
            'param1' => 'value1',
            'param2' => 'value2',
            'signature' => 'invalid_signature_here'
        ];

        $result = $verifier->verify($params);

        $this->assertFalse($result);
    }

    public function testVerifyMissingSignature(): void
    {
        $verifier = new SignatureVerifier($this->testSecret);

        $params = [
            'param1' => 'value1',
            'param2' => 'value2'
        ];

        $result = $verifier->verify($params);

        $this->assertFalse($result);
    }

    public function testVerifySignatureWithDifferentSecret(): void
    {
        $verifier = new SignatureVerifier('different_secret');

        $params = [
            'param1' => 'value1',
            'param2' => 'value2',
            'signature' => hash_hmac('sha256', 'param1=value1&param2=value2', $this->testSecret)
        ];

        $result = $verifier->verify($params);

        $this->assertFalse($result);
    }
}