# Ziyoren Pay SDK

Ziyoren Pay SDK 是一个用于集成Ziyoren支付网关的PHP SDK。

[English Version](README.md) | [中文版本](README_ZH.md)

## 安装

你可以通过composer安装此包：

```bash
composer require ziyoren/pay-sdk
```

## 使用方法

### 初始化客户端

```php
use Ziyoren\PaySdk\Client;

$client = new Client('your-api-key', 'your-secret-key', 'https://api.ziyoren.com');
```

### 创建支付订单

```php
$orderData = [
    'out_trade_no' => 'ORDER-' . time(),
    'total_amount' => 100.00,
    'subject' => '商品名称',
    'notify_url' => 'https://your-domain.com/notify'
];

try {
    $result = $client->createPaymentOrder($orderData);
    // 处理结果
    var_dump($result);
} catch (\Ziyoren\PaySdk\Exceptions\ApiException $e) {
    // 处理API错误
    echo "API错误: " . $e->getMessage();
}
```

### 查询支付状态

```php
try {
    $result = $client->getPaymentStatus('ORDER-123456789');
    // 处理结果
    var_dump($result);
} catch (\Ziyoren\PaySdk\Exceptions\ApiException $e) {
    // 处理API错误
    echo "API错误: " . $e->getMessage();
}
```

### 验证支付通知

```php
// 示例：在通知端点中
$notificationData = $_POST; // 或其他接收数据的方式

if ($client->verifyNotification($notificationData)) {
    // 签名有效，处理通知
    echo "有效的通知";
} else {
    // 签名无效，拒绝
    http_response_code(400);
    echo "无效的签名";
}
```

## 测试

使用PHPUnit运行测试：

```bash
composer test
```

## 贡献

请参阅 [CONTRIBUTING](CONTRIBUTING.md) 了解详情。

## 许可证

MIT许可证。更多信息请参见 [许可证文件](LICENSE)。