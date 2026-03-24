# Ziyoren Pay SDK

A PHP SDK for integrating with Ziyoren Pay payment gateway.

## Installation

You can install the package via composer:

```bash
composer require ziyoren/pay-sdk
```

## Usage

### Initialize Client

```php
use Ziyoren\PaySdk\Client;

$client = new Client('your-api-key', 'your-secret-key', 'https://api.ziyoren.com');
```

### Create Payment Order

```php
$orderData = [
    'out_trade_no' => 'ORDER-' . time(),
    'total_amount' => 100.00,
    'subject' => 'Product Name',
    'notify_url' => 'https://your-domain.com/notify'
];

try {
    $result = $client->createPaymentOrder($orderData);
    // Handle result
    var_dump($result);
} catch (\Ziyoren\PaySdk\Exceptions\ApiException $e) {
    // Handle API error
    echo "API Error: " . $e->getMessage();
}
```

### Get Payment Status

```php
try {
    $result = $client->getPaymentStatus('ORDER-123456789');
    // Handle result
    var_dump($result);
} catch (\Ziyoren\PaySdk\Exceptions\ApiException $e) {
    // Handle API error
    echo "API Error: " . $e->getMessage();
}
```

### Verify Payment Notification

```php
// Example: in your notification endpoint
$notificationData = $_POST; // or however you receive the data

if ($client->verifyNotification($notificationData)) {
    // Signature is valid, process the notification
    echo "Valid notification";
} else {
    // Invalid signature, reject
    http_response_code(400);
    echo "Invalid signature";
}
```

## Testing

Run the tests using PHPUnit:

```bash
composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.