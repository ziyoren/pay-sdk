<?php

namespace Ziyoren\PaySdk;

class SignatureVerifier
{
    private SignatureGenerator $signatureGenerator;

    public function __construct(string $secretKey)
    {
        $this->signatureGenerator = new SignatureGenerator($secretKey);
    }

    public function verify(array $params): bool
    {
        if (!isset($params['signature'])) {
            return false;
        }

        $receivedSignature = $params['signature'];
        $paramsCopy = $params; // 创建副本以保留原始数组
        unset($paramsCopy['signature']); // 移除签名字段，准备重新计算

        $expectedSignature = $this->signatureGenerator->generate($paramsCopy);

        return hash_equals($expectedSignature, $receivedSignature);
    }
}