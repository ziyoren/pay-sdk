<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Ziyoren\PaySdk\Client;

// 示例：创建支付订单
try {
    $client = new Client('your-api-key', 'your-secret-key', 'https://api.ziyoren.com');

    $orderData = [
        'out_trade_no' => 'ORDER-' . time(),
        'total_amount' => 100.00,
        'subject' => 'Test Product',
        'notify_url' => 'https://your-domain.com/notify'
    ];

    $result = $client->createPaymentOrder($orderData);

    echo "Payment order created:\n";
    print_r($result);
} catch (\Ziyoren\PaySdk\Exceptions\ApiException $e) {
    echo "Error creating payment order: " . $e->getMessage() . "\n";
}

// 示例：查询支付状态
try {
    $result = $client->getPaymentStatus('ORDER-123456789');

    echo "Payment status:\n";
    print_r($result);
} catch (\Ziyoren\PaySdk\Exceptions\ApiException $e) {
    echo "Error getting payment status: " . $e->getMessage() . "\n";
}