<?php

declare(strict_types=1);

namespace Cgrate\Php\DTOs;

use Cgrate\Php\Enums\ResponseCode;

/**
 * Data Transfer Object for payment response from Cgrate API.
 */
class PaymentResponseDTO
{
    private int $responseCode;
    private string $responseMessage;
    private ?string $paymentID;
    private ?string $customerMobile;
    private ?string $transactionReference;
    private ?float $transactionAmount;

    /**
     * Create a new payment response DTO.
     *
     * @param int         $responseCode         The response code from the API
     * @param string      $responseMessage      The response message from the API
     * @param string|null $paymentID            The unique ID of the processed payment assigned by Cgrate, if successful
     * @param string|null $customerMobile       Customer mobile number through which payment was processed
     * @param string|null $transactionReference Client-provided transaction reference number
     * @param float|null  $transactionAmount    Transaction amount that was processed
     */
    public function __construct(
        int $responseCode,
        string $responseMessage,
        ?string $paymentID = null,
        ?string $customerMobile = null,
        ?string $transactionReference = null,
        ?float $transactionAmount = null
    ) {
        $this->responseCode = $responseCode;
        $this->responseMessage = $responseMessage;
        $this->paymentID = $paymentID;
        $this->customerMobile = $customerMobile;
        $this->transactionReference = $transactionReference;
        $this->transactionAmount = $transactionAmount;
    }

    public static function fromResponse(array $response): self
    {
        return new self(
            ResponseCode::fromValue($response['responseCode']),
            $response['responseMessage'],
            $response['paymentID'] ?? null,
            $response['customerMobile'] ?? null,
            $response['transactionReference'] ?? null,
            isset($response['transactionAmount']) ? (float) $response['transactionAmount'] : null
        );
    }

    public function isSuccessful(): bool
    {
        return ResponseCode::isSuccess($this->responseCode);
    }

    /**
     * Convert the DTO to an array.
     *
     * @return array{responseCode: int, responseMessage: string,
     * paymentID string|null, customerMobile string|null,
     * transactionReference string|null, transactionAmount float|null}
     */
    public function toArray(): array
    {
        return [
            'responseCode' => $this->responseCode,
            'responseMessage' => $this->responseMessage,
            'paymentID' => $this->paymentID,
            'customerMobile' => $this->customerMobile,
            'transactionReference' => $this->transactionReference,
            'transactionAmount' => $this->transactionAmount,
        ];
    }

    public function getResponseCode(): int
    {
        return $this->responseCode;
    }

    public function getResponseMessage(): string
    {
        return $this->responseMessage;
    }

    public function getPaymentID(): ?string
    {
        return $this->paymentID;
    }

    public function getCustomerMobile(): ?string
    {
        return $this->customerMobile;
    }

    public function getTransactionReference(): ?string
    {
        return $this->transactionReference;
    }

    public function getTransactionAmount(): ?float
    {
        return $this->transactionAmount;
    }
}
