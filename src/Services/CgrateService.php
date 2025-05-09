<?php

declare(strict_types=1);

namespace Cgrate\Php\Services;

use Cgrate\Php\DTOs\BalanceResponseDTO;
use Cgrate\Php\DTOs\PaymentRequestDTO;
use Cgrate\Php\DTOs\PaymentResponseDTO;
use Cgrate\Php\DTOs\ReversePaymentResponseDTO;
use Cgrate\Php\Exceptions\ConnectionException;
use Cgrate\Php\Exceptions\InvalidResponseException;
use Cgrate\Php\Validation\ConfigValidator;
use Cgrate\Php\Validation\PaymentValidator;
use SoapClient;
use SoapFault;
use SoapHeader;
use SoapVar;

/**
 * Service for interacting with the Cgrate API.
 *
 * This service provides methods to perform operations with Cgrate payment gateway
 * including getting account balance, processing customer payments, querying
 * transaction status and reversing payments.
 */
class CgrateService
{
    private ?SoapClient $client = null;

    /**
     * Create a new Cgrate service instance.
     *
     * @param  array $config The configuration array
     * @throws \Cgrate\Php\Exceptions\ValidationException If configuration is invalid
     */
    public function __construct(array $config)
    {
        $validatedConfig = ConfigValidator::validate($config);

        $this->initializeClient($validatedConfig);
    }

    /**
     * Get the account balance from Cgrate.
     *
     * @return BalanceResponseDTO The account balance response
     * @throws \Cgrate\Php\Exceptions\ConnectionException If connection to the API fails
     * @throws \Cgrate\Php\Exceptions\InvalidResponseException If the API returns an error response
     */
    public function getAccountBalance(): BalanceResponseDTO
    {
        try {
            $response = $this->client->getAccountBalance();

            if (!is_object($response) || !property_exists($response, 'return')) {
                throw InvalidResponseException::unexpectedFormat('getAccountBalance');
            }

            $dto = BalanceResponseDTO::fromResponse((array) $response->return);

            if (!$dto->isSuccessful()) {
                throw InvalidResponseException::fromResponseCode($dto->getResponseCode());
            }

            return $dto;
        } catch (\SoapFault $e) {
            throw ConnectionException::fromSoapFault($e, 'Failed to get account balance');
        }
    }

    /**
     * Process a customer payment.
     *
     * @param  PaymentRequestDTO $payment The payment request data
     * @return PaymentResponseDTO The payment response
     * @throws \Cgrate\Php\Exceptions\ConnectionException If connection to the API fails
     * @throws \Cgrate\Php\Exceptions\InvalidResponseException If the API returns an error response
     * @throws \Cgrate\Php\Exceptions\ValidationException If the payment request is invalid
     */
    public function processCustomerPayment(PaymentRequestDTO $payment): PaymentResponseDTO
    {
        try {
            PaymentValidator::validate($payment);

            $response = $this->client->processCustomerPayment($payment->toArray());

            if (!is_object($response) || !property_exists($response, 'return')) {
                throw InvalidResponseException::unexpectedFormat('processCustomerPayment');
            }

            $dto = PaymentResponseDTO::fromResponse(
                (array) $response->return + [
                    'customerMobile' => $payment->getCustomerMobile(),
                    'transactionReference' => $payment->getPaymentReference(),
                    'transactionAmount' => $payment->getTransactionAmount(),
                ]
            );

            if (!$dto->isSuccessful()) {
                throw InvalidResponseException::fromResponseCode($dto->getResponseCode());
            }

            return $dto;
        } catch (SoapFault $e) {
            throw ConnectionException::fromSoapFault($e, 'Failed to process customer payment');
        }
    }

    /**
     * Query the status of a transaction.
     *
     * @param  string $transactionReference The reference of the transaction to query
     * @return PaymentResponseDTO The transaction status response
     * @throws \Cgrate\Php\Exceptions\ConnectionException If connection to the API fails
     * @throws \Cgrate\Php\Exceptions\InvalidResponseException If the API returns an error response
     */
    public function queryTransactionStatus(string $transactionReference): PaymentResponseDTO
    {
        try {
            $response = $this->client->queryTransactionStatus(
                [
                    'transactionReference' => $transactionReference,
                ]
            );

            if (!is_object($response) || !property_exists($response, 'return')) {
                throw InvalidResponseException::unexpectedFormat('queryTransactionStatus');
            }

            $dto = PaymentResponseDTO::fromResponse(
                (array) $response->return + [
                    'transactionReference' => $transactionReference,
                ]
            );

            if (!$dto->isSuccessful()) {
                throw InvalidResponseException::fromResponseCode($dto->getResponseCode());
            }

            return $dto;
        } catch (SoapFault $e) {
            throw ConnectionException::fromSoapFault($e, 'Failed to query transaction status');
        }
    }

    /**
     * Reverse a customer payment.
     *
     * @param  string $paymentReference The reference of the payment to reverse
     * @return ReversePaymentResponseDTO The reverse payment response
     * @throws \Cgrate\Php\Exceptions\ConnectionException If connection to the API fails
     * @throws \Cgrate\Php\Exceptions\InvalidResponseException If the API returns an error response
     */
    public function reverseCustomerPayment(string $paymentReference): ReversePaymentResponseDTO
    {
        try {
            $response = $this->client->reverseCustomerPayment(
                [
                    'paymentReference' => $paymentReference,
                ]
            );

            if (!is_object($response) || !property_exists($response, 'return')) {
                throw InvalidResponseException::unexpectedFormat('reverseCustomerPayment');
            }

            $dto = ReversePaymentResponseDTO::fromResponse(
                (array) $response->return + [
                    'transactionReference' => $paymentReference,
                ]
            );

            if (!$dto->isSuccessful()) {
                throw InvalidResponseException::fromResponseCode($dto->getResponseCode());
            }

            return $dto;
        } catch (SoapFault $e) {
            throw ConnectionException::fromSoapFault($e, 'Failed to reverse customer payment');
        }
    }

    /**
     * Initialize the SOAP client.
     *
     * @param array $config The configuration array
     */
    private function initializeClient(array $config): void
    {
        if ($this->client !== null) {
            return;
        }

        $wsseNs = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd';
        $securityXml = new SoapVar(
            '<wsse:Security xmlns:wsse="' . $wsseNs . '" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd" soapenv:mustUnderstand="1" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
                        <wsse:UsernameToken wsu:Id="UsernameToken-1">
                            <wsse:Username>' . $config['username'] . '</wsse:Username>
                            <wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">' . $config['password'] . '</wsse:Password>
                        </wsse:UsernameToken>
                    </wsse:Security>',
            XSD_ANYXML
        );

        $endpoint = $this->getEndpoint($config);
        $this->client = new SoapClient($endpoint, $config['options'] + ['location' => $endpoint]);
        $this->client->__setSoapHeaders([new SoapHeader($wsseNs, 'Security', $securityXml, true)]);
    }

    /**
     * Get the appropriate API endpoint based on configuration.
     *
     * @param  array $config The configuration array
     * @return string The endpoint URL
     */
    private function getEndpoint(array $config): string
    {
        return $config['test_mode'] ? $config['test_endpoint'] : $config['endpoint'];
    }
}
