<?php

namespace Ziyoren\PaySdk\Tests;

use PHPUnit\Framework\TestCase;
use Ziyoren\PaySdk\SignatureGenerator;

class SignatureGeneratorTest extends TestCase
{
    private string $testSecret = 'test_secret_key';

    public function testGenerateSignature(): void
    {
        $generator = new SignatureGenerator($this->testSecret);

        $params = [
            'param1' => 'value1',
            'param2' => 'value2'
        ];

        $signature = $generator->generate($params);

        $this->assertIsString($signature);
        $this->assertEquals(64, strlen($signature)); // SHA256 produces 64 char hex string
    }

    public function testGenerateSignatureWithEmptyArray(): void
    {
        $generator = new SignatureGenerator($this->testSecret);

        $signature = $generator->generate([]);

        $this->assertIsString($signature);
    }

    public function testGenerateSignatureFiltersSignatureField(): void
    {
        $generator = new SignatureGenerator($this->testSecret);

        $params = [
            'param1' => 'value1',
            'signature' => 'some_existing_signature',
            'param2' => 'value2'
        ];

        $signature = $generator->generate($params);

        // The generated signature should not include the 'signature' field from input
        $expected = hash_hmac('sha256', 'param1=value1&param2=value2', $this->testSecret);
        $this->assertEquals($expected, $signature);
    }

    public function testGenerateSignatureWithArrayValue(): void
    {
        $generator = new SignatureGenerator($this->testSecret);

        $params = [
            'param1' => 'value1',
            'param2' => ['nested' => 'value']
        ];

        $signature = $generator->generate($params);

        $this->assertIsString($signature);
        $this->assertEquals(64, strlen($signature));
    }
}