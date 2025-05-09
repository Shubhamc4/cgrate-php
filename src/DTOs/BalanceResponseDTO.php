<?php

declare(strict_types=1);

namespace Cgrate\Php\DTOs;

use Cgrate\Php\Enums\ResponseCode;

/**
 * Data Transfer Object for balance inquiry response from Cgrate API.
 */
class BalanceResponseDTO
{
    private int $responseCode;
    private string $responseMessage;
    private ?float $balance;

    /**
     * Create a new balance response DTO.
     *
     * @param int        $responseCode    The response code from the API
     * @param string     $responseMessage The response message from the API
     * @param float|null $balance         The account balance, if successful
     */
    public function __construct(
        int $responseCode,
        string $responseMessage,
        ?float $balance = null
    ) {
        $this->responseCode = $responseCode;
        $this->responseMessage = $responseMessage;
        $this->balance = $balance;
    }

    public static function fromResponse(array $response): self
    {
        return new self(
            ResponseCode::fromValue($response['responseCode']),
            $response['responseMessage'],
            isset($response['balance']) ? (float) $response['balance'] : null
        );
    }

    public function isSuccessful(): bool
    {
        return ResponseCode::isSuccess($this->responseCode);
    }

    /**
     * Convert the DTO to an array.
     *
     * @return array{responseCode: int, responseMessage: string, balance: string}
     */
    public function toArray(): array
    {
        return [
            'responseCode' => $this->responseCode,
            'responseMessage' => $this->responseMessage,
            'balance' => 'ZMW ' . number_format($this->balance ?? 0, 2),
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

    public function getBalance(): ?float
    {
        return $this->balance;
    }
}
