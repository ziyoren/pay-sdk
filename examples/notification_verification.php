<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Ziyoren\PaySdk\Client;

// 示例：验证支付通知
$client = new Client('your-api-key', 'your-secret-key', 'https://api.ziyoren.com');

// 假设这是从支付网关收到的通知数据
$notificationData = [
    'out_trade_no' => 'ORDER-123456789',
    'trade_no' => 'TRADE-987654321',
    'total_amount' => 100.00,
    'status' => 'success',
    'signature' => 'calculated_signature_from_gateway'
];

if ($client->verifyNotification($notificationData)) {
    echo "Valid notification - proceed with order fulfillment\n";
    // Process the successful payment here
} else {
    echo "Invalid notification - possible security threat\n";
    http_response_code(400);
}