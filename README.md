# CGrate PHP Package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/shubhamc4/cgrate-php.svg)](https://packagist.org/packages/shubhamc4/cgrate-php)
[![Total Downloads](https://img.shields.io/packagist/dt/shubhamc4/cgrate-php.svg)](https://packagist.org/packages/shubhamc4/cgrate-php)
[![License](https://img.shields.io/packagist/l/shubhamc4/cgrate-php.svg)](https://github.com/shubhamc4/cgrate-php/blob/main/LICENSE)

A Core PHP package for integrating with the CGrate payment service to process mobile money transactions in Zambia.

## Table of Contents

- [Introduction](#introduction)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
  - [Check Account Balance](#check-account-balance)
  - [Process Customer Payment](#process-customer-payment)
  - [Query Transaction Status](#query-transaction-status)
  - [Reverse Customer Payment](#reverse-customer-payment)
- [Data Transfer Objects](#data-transfer-objects)
- [Changelog](#changelog)
- [Credits](#credits)
- [License](#license)

## Introduction

[CGrate](https://cgrate.co.zm) ([543 Konse Konse](https://www.543.co.zm)) is a payment service provider based in Zambia that facilitates mobile money transactions. This Laravel package allows businesses to:

- Process payments from mobile money accounts
- Check account balances in real-time
- Verify transaction status
- Reverse/refund payments when necessary

The service operates via a SOAP API that requires WS-Security authentication. CGrate is widely used for integrating with local payment systems in Zambia, making it easier for businesses to accept mobile payments from customers.

For more information about CGrate payment service, visit their [official website](https://cgrate.co.zm) or contact their support team at support@cgrate.co.zm.

### Official Documentation

For detailed information on the CGrate SOAP API, including setup instructions, request formats, and response codes, please refer to the official [EVDSpec 2024.pdf](./docs/EVDSpec_2024.pdf) document. This comprehensive guide provides all the technical specifications required for integrating with the CGrate service.

## Requirements

- PHP 8.2 or higher
- PHP SOAP extension

## Installation

```bash
composer require shubhamc4/cgrate-php
```

## Configuration

```php
$config = [
    'username' => 'your-username',  // Required
    'password' => 'your-password',  // Required
    'endpoint' => 'https://543.cgrate.co.zm/Konik/KonikWs?wsdl',  // Production endpoint
    'testEndpoint' => 'http://test.543.cgrate.co.zm:55555/Konik/KonikWs?wsdl',  // Test endpoint
    'testMode' => false,  // Set to true for test environment
    'options' => [  // SOAP client options
        'soap_version' => SOAP_1_1,
        'connection_timeout' => 30,
        'keep_alive' => false,
        'cache_wsdl' => WSDL_CACHE_NONE,
        'exceptions' => true,
        'trace' => false,  // Set to true for test environment
    ],
];

$client = new \CGrate\Php\Services\CGrateService($config);
```

Alternatively, you can use the config helper:

```php
use CGrate\Php\Config\CGrateConfig;
use CGrate\Php\Services\CGrateService;

// Create config with username, password and test mode (optional)
$config = CGrateConfig::create(
    username: 'your-username',
    password: 'your-password',
    testMode: true
);
$client = new CGrateService($config);
```

## Available Methods

| Method                                                 | Description                    |
| ------------------------------------------------------ | ------------------------------ |
| `getAccountBalance()`                                  | Get the account balance        |
| `processCustomerPayment(PaymentRequestDTO $payment)`   | Process a new customer payment |
| `queryTransactionStatus(string $transactionReference)` | Check the status of a payment  |
| `reverseCustomerPayment(string $paymentReference)`     | Reverse a customer payment     |

## Usage

### Check Account Balance

```php
try {
    $response = $client->getAccountBalance();

    if ($response->isSuccessful()) {
        echo "Balance: " . $response->displayBalance();
    } else {
        echo "Error: " . $response->responseMessage;
    }
} catch (\CGrate\Php\Exceptions\CGrateException $e) {
    echo "Exception: " . $e->getMessage();
}
```

### Process Customer Payment

```php
try {
    $payment = new \CGrate\Php\DTOs\PaymentRequestDTO(
        10.50,  // Amount
        '260970000000',  // Customer mobile number
        'PAYMENT-' . time()  // Unique payment reference
    );

    $response = $client->processCustomerPayment($payment);

    if ($response->isSuccessful()) {
        echo "Payment ID: " . $response->paymentID;
    } else {
        echo "Payment failed: " . $response->responseMessage;
    }
} catch (\CGrate\Php\Exceptions\CGrateException $e) {
    echo "Exception: " . $e->getMessage();
}
```

### Query Transaction Status

```php
try {
    $response = $client->queryTransactionStatus('YOUR-TRANSACTION-REFERENCE');

    if ($response->isSuccessful()) {
        echo "Transaction status: " . $response->responseMessage;
    } else {
        echo "Query failed: " . $response->responseMessage;
    }
} catch (\CGrate\Php\Exceptions\CGrateException $e) {
    echo "Exception: " . $e->getMessage();
}
```

### Reverse Customer Payment

```php
try {
    $response = $client->reverseCustomerPayment('YOUR-PAYMENT-REFERENCE');

    if ($response->isSuccessful()) {
        echo "Payment reversed successfully";
    } else {
        echo "Reversal failed: " . $response->responseMessage;
    }
} catch (\CGrate\Php\Exceptions\CGrateException $e) {
    echo "Exception: " . $e->getMessage();
}
```

## Data Transfer Objects

The package uses DTOs to handle API requests and responses:

### Request DTOs

- `PaymentRequestDTO`: Contains payment request data (transactionAmount, customerMobile, paymentReference)

### Response DTOs

- `BalanceResponseDTO`: Contains account balance information
- `PaymentResponseDTO`: Contains payment response information
- `ReversePaymentResponseDTO`: Contains payment reversal response information

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [Shubham Chaudhary](https://github.com/shubhamc4)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
