<?php

declare(strict_types=1);

namespace Cgrate\Php\DTOs;

use Cgrate\Php\Enums\ResponseCode;

/**
 * Data Transfer Object for reverse payment response from Cgrate API.
 */
class ReversePaymentResponseDTO
{
    private int $responseCode;
    private string $responseMessage;
    private ?string $transactionReference;

    /**
     * Create a new reverse payment response DTO.
     *
     * @param int         $responseCode         The response code from the API
     * @param string      $responseMessage      The response message from the API
     * @param string|null $transactionReference The transaction reference that was reversed
     */
    public function __construct(
        int $responseCode,
        string $responseMessage,
        ?string $transactionReference = null
    ) {
        $this->responseCode = $responseCode;
        $this->responseMessage = $responseMessage;
        $this->transactionReference = $transactionReference;
    }

    public static function fromResponse(array $response): self
    {
        return new self(
            ResponseCode::fromValue($response['responseCode']),
            $response['responseMessage'],
            $response['transactionReference'] ?? null
        );
    }

    public function isSuccessful(): bool
    {
        return ResponseCode::isSuccess($this->responseCode);
    }

    /**
     * Convert the DTO to an array.
     *
     * @return array{responseCode: int, responseMessage: string, transactionReference: string}
     */
    public function toArray(): array
    {
        return [
            'responseCode' => $this->responseCode,
            'responseMessage' => $this->responseMessage,
            'transactionReference' => $this->transactionReference,
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

    public function getTransactionReference(): ?string
    {
        return $this->transactionReference;
    }
}
