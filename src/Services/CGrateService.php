<?php

declare(strict_types=1);

namespace CGrate\Php\Services;

use CGrate\Php\DTOs\BalanceResponseDTO;
use CGrate\Php\DTOs\PaymentRequestDTO;
use CGrate\Php\DTOs\PaymentResponseDTO;
use CGrate\Php\DTOs\CashDepositRequestDTO;
use CGrate\Php\DTOs\CashDepositResponseDTO;
use CGrate\Php\Exceptions\ConnectionException;
use CGrate\Php\Exceptions\InvalidResponseException;
use CGrate\Php\Validation\CashDepositValidator;
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
        $data = $this->callSoap(
            'getAccountBalance',
            'Failed to get account balance'
        );

        $dto = BalanceResponseDTO::fromResponse($data);

        if (! $dto->isSuccessful()) {
            throw InvalidResponseException::fromResponseCode($dto->responseCode);
        }

        return $dto;
    }

    /**
     * Get available cash deposit issuers from CGrate.
     *
     * @return  string[]  The list of available cash deposit issuer names
     * @throws  \CGrate\Php\Exceptions\ConnectionException  If connection to the API fails
     * @throws  \CGrate\Php\Exceptions\InvalidResponseException  If the API returns an unexpected response
     */
    public function getAvailableCashDepositIssuers(): array
    {
        $issuers = $this->callSoap(
            'getAvailableCashDepositIssuers',
            'Failed to get available cash deposit issuers'
        );

        if (empty($issuers)) {
            throw InvalidResponseException::unexpectedFormat('getAvailableCashDepositIssuers');
        }

        foreach ($issuers as $issuer) {
            if (! is_string($issuer) || trim($issuer) === '') {
                throw InvalidResponseException::unexpectedFormat('getAvailableCashDepositIssuers');
            }
        }

        return $issuers;
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
        PaymentValidator::validate($payment);

        $data = $this->callSoap(
            'processCustomerPayment',
            'Failed to process customer payment',
            $payment->toArray()
        );

        $dto = PaymentResponseDTO::fromResponse($data + [
            'customerMobile' => $payment->customerMobile,
            'transactionReference' => $payment->paymentReference,
            'transactionAmount' => $payment->transactionAmount,
        ]);

        if (! $dto->isSuccessful()) {
            throw InvalidResponseException::fromResponseCode($dto->responseCode);
        }

        return $dto;
    }

    /**
     * Query the status of a customer transaction.
     *
     * @param  string $transactionReference The reference of the transaction to query
     * @return PaymentResponseDTO The transaction status response
     * @throws \CGrate\Php\Exceptions\ConnectionException If connection to the API fails
     * @throws \CGrate\Php\Exceptions\InvalidResponseException If the API returns an error response
     */
    public function queryCustomerPayment(string $transactionReference): PaymentResponseDTO
    {
        $data = $this->callSoap(
            'queryCustomerPayment',
            'Failed to query transaction status',
            ['paymentReference' => $transactionReference]
        );

        $dto = PaymentResponseDTO::fromResponse($data + ['transactionReference' => $transactionReference]);

        if (! $dto->isSuccessful()) {
            throw InvalidResponseException::fromResponseCode($dto->responseCode);
        }

        return $dto;
    }

    /**
     * Cash deposit to a customer account.
     *
     * @param  \CGrate\Php\DTOs\CashDepositRequestDTO  $cashDeposit  The cash deposit request data
     * @return  \CGrate\Php\DTOs\CashDepositResponseDTO  The cash deposit response
     * @throws  \CGrate\Php\Exceptions\ConnectionException  If connection to the API fails
     * @throws  \CGrate\Php\Exceptions\InvalidResponseException  If the API returns an error response
     */
    public function processCashDeposit(CashDepositRequestDTO $cashDeposit): CashDepositResponseDTO
    {
        CashDepositValidator::validate($cashDeposit);

        $data = $this->callSoap(
            'processCashDeposit',
            'Failed to process cash deposit',
            $cashDeposit->toArray()
        );

        $dto = CashDepositResponseDTO::fromResponse($data + [
            'depositorReference' => $cashDeposit->depositorReference,
        ]);

        if (! $dto->isSuccessful()) {
            throw InvalidResponseException::fromResponseCode($dto->responseCode);
        }

        return $dto;
    }

    /**
     * Generate a unique transaction reference.
     *
     * @param  string  $prefix  The prefix for the transaction reference
     * @return  string  The unique generated transaction reference
     */
    public static function generateTransactionReference(string $prefix = 'CG'): string
    {
        return sprintf(
            rtrim($prefix, '-').'-%d-%s',
            round(microtime(true) * 100),
            bin2hex(random_bytes(6))
        );
    }

    /**
     * Determines the issuer name for a 543 payment service based on the customer's contact number.
     *
     * @param  string  $customerAccount  The customer's mobile number (e.g., "260XXXXXXXXX")
     * @return  string  The identified issuer name from the provided list, or "Unknown Issuer"
     */
    public static function getCustomerIssuerName(string $customerAccount): string
    {
        if (! preg_match('/^(?:260|0)?(\d{2})\d*$/', trim($customerAccount), $prefix)) {
            return 'Unknown Issuer';
        }

        return match ($prefix[1]) {
            '97', '77', '57' => 'Airtel',
            '76', '96' => 'MTN',
            '95', '75' => 'Zamtel',
            default => 'Unknown Issuer',
        };
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

    /**
     * Execute a SOAP method, validate the response, and return the result as an array.
     *
     * @param  string  $soapMethod    The SOAP method name to call on the client
     * @param  string  $errorContext  Human-readable context string used in error messages
     * @param  array|null  $params    Parameters to pass to the SOAP method, or null for no parameters
     * @return  array  The response->return property cast to an array
     * @throws  \CGrate\Php\Exceptions\ConnectionException  If a SoapFault occurs
     * @throws  \CGrate\Php\Exceptions\InvalidResponseException  If the response structure is invalid
     */
    private function callSoap(string $soapMethod, string $errorContext, ?array $params = null): array
    {
        try {
            $response = $params !== null
                ? $this->client->{$soapMethod}($params)
                : $this->client->{$soapMethod}();

            $this->validateResponse($response, $errorContext);

            return (array) $response->return;
        } catch (SoapFault $e) {
            throw ConnectionException::fromSoapFault($e, $errorContext);
        }
    }

    /**
     * Validate the soap client response and check if the return property exists
     *
     * @param  object  $response  soap client response object
     * @param  string  $method  method name where this is called
     * @return  void
     */
    private function validateResponse(object $response, string $method): void
    {
        if (! is_object($response) || ! property_exists($response, 'return')) {
            throw InvalidResponseException::unexpectedFormat($method);
        }

        if ($response->return === null) {
            throw InvalidResponseException::unexpectedFormat($method);
        }
    }
}
