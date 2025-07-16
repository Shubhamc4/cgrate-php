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
  - [Get Available Cash Deposit Issuers](#get-available-cash-deposit-issuers)
  - [Process Customer Payment](#process-customer-payment)
  - [Query Customer Payment](#query-customer-payment)
  - [Process Cash Deposit](#process-cash-deposit)
  - [Generate Transaction Reference](#generate-transaction-reference)
  - [Get Customer Account Issuer Name](#get-customer-issuer-name)
- [Data Transfer Objects](#data-transfer-objects)
- [Response Codes](#response-codes)
- [Changelog](#changelog)
- [Credits](#credits)
- [License](#license)

## Introduction

[CGrate](https://cgrate.co.zm) ([543 Konse Konse](https://www.543.co.zm)) is a payment service provider based in Zambia that facilitates mobile money transactions. This Core PHP package allows businesses to:

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

## Available Soap Methods

| Method                                               | Description                            |
| ---------------------------------------------------- | -------------------------------------- |
| `getAccountBalance()`                                | Get the account balance                |
| `getAvailableCashDepositIssuers()`                   | Get Available Cash Deposit Issuers     |
| `processCustomerPayment(PaymentRequestDTO $payment)` | Process a new customer payment         |
| `queryCustomerPayment(string $transactionReference)` | Check the status of a customer payment |
| `processCashDeposit(string $paymentReference)`       | Process Cash Deposit                   |

## Available Static Helper Methods

| Method                                                | Description                             |
| ----------------------------------------------------- | --------------------------------------- |
| `generateTransactionReference(string $prefix = 'CG')` | Generate a unique transaction reference |
| `getCustomerIssuerName(string $customerAccount)`      | Get Customer Account Issuer Name        |

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

### Get Available Cash Deposit Issuers

```php
try {
    $response = $client->getAvailableCashDepositIssuers();
    print_r($response);
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

### Query Customer Payment

```php
try {
    $response = $client->queryCustomerPayment('YOUR-TRANSACTION-REFERENCE');

    if ($response->isSuccessful()) {
        echo "Transaction status: " . $response->responseMessage;
    } else {
        echo "Query failed: " . $response->responseMessage;
    }
} catch (\CGrate\Php\Exceptions\CGrateException $e) {
    echo "Exception: " . $e->getMessage();
}
```

### Process Cash Deposit

```php
try {
    $customerAccount = '260970000000';  // Customer mobile number
    $cashDeposit = new \CGrate\Php\DTOs\CashDepositRequestDTO(
        10.50,  // Amount
        $customerAccount,
        \CGrate\Php\Services\CGrateService::getCustomerIssuerName($customerAccount),
        'CD-' . time()  // Unique cash deposit reference
    );

    $response = $client->processCashDeposit($cashDeposit);

    if ($response->isSuccessful()) {
        echo "Depositor reference: " . $response->depositorReference;
    } else {
        echo "Cash deposit failed: " . $response->responseMessage;
    }
} catch (\CGrate\Php\Exceptions\CGrateException $e) {
    echo "Exception: " . $e->getMessage();
}
```

### Generate Transaction Reference

```php
// Generate a reference with the default prefix 'CG'
$reference = \CGrate\Php\Services\CGrateService::generateTransactionReference();
// Result: CG-1714504562-a1b2c3d4e5f6

// Generate a reference with a custom prefix
$reference = \CGrate\Php\Services\CGrateService::generateTransactionReference('ORDER');
// Result: ORDER-1714504562-a1b2c3d4e5f6
```

### Get Customer Account Issuer Name

```php
// Get issuer name using the the customer account

$issuerName = \CGrate\Php\Services\CGrateService::getCustomerIssuerName('26097XXXXXXX');
// Result: Airtel

$issuerName = \CGrate\Php\Services\CGrateService::getCustomerIssuerName('26076XXXXXXX');
// Result: MTN

$issuerName = \CGrate\Php\Services\CGrateService::getCustomerIssuerName('26095XXXXXXX');
// Result: Zamtel

$issuerName = \CGrate\Php\Services\CGrateService::getCustomerIssuerName('26065XXXXXXX');
// Result: Unknown Issuer
```

## Data Transfer Objects

The package uses read-only DTOs to handle API requests and responses:

### Request DTOs

- `PaymentRequestDTO`: Contains payment request data (transactionAmount, customerMobile, paymentReference)
- `CashDepositRequestDTO`: Contains cash deposit request data (transactionAmount, customerAccount, issuerName, depositorReference)

### Response DTOs

- `BalanceResponseDTO`: Contains account balance information
- `PaymentResponseDTO`: Contains payment response information
- `CashDepositResponseDTO`: Contains cash deposit response information

## Response Codes

The package includes a comprehensive `ResponseCode` enum that provides all possible response codes from the CGrate API along with their descriptions:

```php
use CGrate\Php\Enums\ResponseCode;

// Check if a response matches a specific code
if ($response->responseCode->is(ResponseCode::SUCCESS)) {
    // Handle successful response
}

// Get the description for any response code
$description = ResponseCode::descriptionFromValue(0); // "Success"
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [Shubham Chaudhary](https://github.com/shubhamc4)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
