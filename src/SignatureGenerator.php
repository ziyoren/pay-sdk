<?php

namespace Ziyoren\PaySdk;

use Ziyoren\PaySdk\Exceptions\SignatureException;

class SignatureGenerator
{
    private string $secretKey;

    public function __construct(string $secretKey)
    {
        $this->secretKey = $secretKey;
    }

    public function generate(array $params): string
    {
        $paramsToSign = $this->filterAndSortParams($params);
        $signStr = $this->buildSignatureString($paramsToSign);

        return hash_hmac('sha256', $signStr, $this->secretKey);
    }

    private function filterAndSortParams(array $params): array
    {
        // 移除空值和签名字段
        $filtered = [];
        foreach ($params as $key => $value) {
            if ($key !== 'signature' && $value !== '' && $value !== null) {
                $filtered[$key] = $value;
            }
        }

        // 按键名升序排序
        ksort($filtered);

        return $filtered;
    }

    private function buildSignatureString(array $params): string
    {
        $pairs = [];
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }
            $pairs[] = "{$key}={$value}";
        }

        return implode('&', $pairs);
    }
}