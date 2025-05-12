<?php

declare(strict_types=1);

namespace CGrate\Php\Services;

use CGrate\Php\DTOs\BalanceResponseDTO;
use CGrate\Php\DTOs\PaymentRequestDTO;
use CGrate\Php\DTOs\PaymentResponseDTO;
use CGrate\Php\DTOs\ReversePaymentResponseDTO;
use CGrate\Php\Exceptions\ConnectionException;
use CGrate\Php\Exceptions\InvalidResponseException;
use CGrate\Php\Validation\ConfigValidator;
use CGrate\Php\Validation\PaymentValidator;
use SoapClient;
use SoapFault;
use SoapHeader;
use SoapVar;

/**
 * Service for interacting with the CGrate API.
 *
 * This service provides methods to perform operations with CGrate payment gateway
 * including getting account balance, processing customer payments, querying
 * transaction status and reversing payments.
 */
final class CGrateService
{
    private ?SoapClient $client = null;

    /**
     * Create a new CGrate service instance.
     *
     * @param  array{username:string,password:string,endpoint:string,testEndpoint:string,options:array}
     * $config  The configuration array
     * @throws  \CGrate\Php\Exceptions\ValidationException  If configuration is invalid
     */
    public function __construct(array $config)
    {
        $validatedConfig = ConfigValidator::validate($config);

        $this->initializeClient($validatedConfig);
    }

    /**
     * Get the account balance from CGrate.
     *
     * @return  \CGrate\Php\DTOs\BalanceResponseDTO  The account balance response
     * @throws  \CGrate\Php\Exceptions\ConnectionException  If connection to the API fails
     * @throws  \CGrate\Php\Exceptions\InvalidResponseException  If the API returns an error response
     */
    public function getAccountBalance(): BalanceResponseDTO
    {
        try {
            $response = $this->client->getAccountBalance();

            if (! is_object($response) || ! property_exists($response, 'return')) {
                throw InvalidResponseException::unexpectedFormat('getAccountBalance');
            }

            $dto = BalanceResponseDTO::fromResponse((array) $response->return);

            if (! $dto->isSuccessful()) {
                throw InvalidResponseException::fromResponseCode($dto->responseCode);
            }

            return $dto;
        } catch (SoapFault $e) {
            throw ConnectionException::fromSoapFault($e, 'Failed to get account balance');
        }
    }

    /**
     * Process a customer payment.
     *
     * @param  \CGrate\Php\DTOs\PaymentRequestDTO  $payment  The payment request data
     * @return  \CGrate\Php\DTOs\PaymentResponseDTO  The payment response
     * @throws  \CGrate\Php\Exceptions\ConnectionException  If connection to the API fails
     * @throws  \CGrate\Php\Exceptions\InvalidResponseException  If the API returns an error response
     * @throws  \CGrate\Php\Exceptions\ValidationException  If the payment request is invalid
     */
    public function processCustomerPayment(PaymentRequestDTO $payment): PaymentResponseDTO
    {
        try {
            PaymentValidator::validate($payment);

            $response = $this->client->processCustomerPayment($payment->toArray());

            if (! is_object($response) || ! property_exists($response, 'return')) {
                throw InvalidResponseException::unexpectedFormat('processCustomerPayment');
            }

            $dto = PaymentResponseDTO::fromResponse(
                (array) $response->return + [
                    'customerMobile' => $payment->customerMobile,
                    'transactionReference' => $payment->paymentReference,
                    'transactionAmount' => $payment->transactionAmount,
                ]
            );

            if (! $dto->isSuccessful()) {
                throw InvalidResponseException::fromResponseCode($dto->responseCode);
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
     * @throws \CGrate\Php\Exceptions\ConnectionException If connection to the API fails
     * @throws \CGrate\Php\Exceptions\InvalidResponseException If the API returns an error response
     */
    public function queryTransactionStatus(string $transactionReference): PaymentResponseDTO
    {
        try {
            $response = $this->client->queryTransactionStatus(
                ['transactionReference' => $transactionReference]
            );

            if (! is_object($response) || ! property_exists($response, 'return')) {
                throw InvalidResponseException::unexpectedFormat('queryTransactionStatus');
            }

            $dto = PaymentResponseDTO::fromResponse(
                (array) $response->return + ['transactionReference' => $transactionReference]
            );

            if (! $dto->isSuccessful()) {
                throw InvalidResponseException::fromResponseCode($dto->responseCode);
            }

            return $dto;
        } catch (SoapFault $e) {
            throw ConnectionException::fromSoapFault($e, 'Failed to query transaction status');
        }
    }

    /**
     * Reverse a customer payment.
     *
     * @param  string  $paymentReference  The reference of the payment to reverse
     * @return  \CGrate\Php\DTOs\ReversePaymentResponseDTO  The reverse payment response
     * @throws  \CGrate\Php\Exceptions\ConnectionException  If connection to the API fails
     * @throws  \CGrate\Php\Exceptions\InvalidResponseException  If the API returns an error response
     */
    public function reverseCustomerPayment(string $paymentReference): ReversePaymentResponseDTO
    {
        try {
            $response = $this->client->reverseCustomerPayment(
                ['paymentReference' => $paymentReference]
            );

            if (! is_object($response) || ! property_exists($response, 'return')) {
                throw InvalidResponseException::unexpectedFormat('reverseCustomerPayment');
            }

            $dto = ReversePaymentResponseDTO::fromResponse(
                (array) $response->return + [
                    'transactionReference' => $paymentReference,
                ]
            );

            if (! $dto->isSuccessful()) {
                throw InvalidResponseException::fromResponseCode($dto->responseCode);
            }

            return $dto;
        } catch (SoapFault $e) {
            throw ConnectionException::fromSoapFault($e, 'Failed to reverse customer payment');
        }
    }

    /**
     * Initialize the SOAP client.
     *
     * @param  array{username:string,password:string,endpoint:string,testEndpoint:string,options:array}
     * $config The configuration array
     */
    private function initializeClient(array $config): void
    {
        if ($this->client !== null) {
            return;
        }

        $wsseNs = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd';
        $securityXml = new SoapVar(
            '<wsse:Security xmlns:wsse="'.$wsseNs.'" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd" soapenv:mustUnderstand="1" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
                        <wsse:UsernameToken wsu:Id="UsernameToken-1">
                            <wsse:Username>'.$config['username'].'</wsse:Username>
                            <wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">'.$config['password'].'</wsse:Password>
                        </wsse:UsernameToken>
                    </wsse:Security>',
            XSD_ANYXML
        );

        $endpoint = $this->getEndpoint($config);

        $this->client = new SoapClient(
            $endpoint,
            $config['options'] + ['location' => $endpoint]
        );

        $this->client->__setSoapHeaders(
            [new SoapHeader($wsseNs, 'Security', $securityXml, true)]
        );
    }

    /**
     * Get the appropriate API endpoint based on configuration.
     *
     * @param  array{username:string,password:string,endpoint:string,testEndpoint:string,options:array}
     * $config The configuration array
     * @return  string  The endpoint URL
     */
    private function getEndpoint(array $config): string
    {
        return $config['testMode'] ? $config['testEndpoint'] : $config['endpoint'];
    }
}
