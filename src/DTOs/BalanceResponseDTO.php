<?php

declare(strict_types=1);

namespace CGrate\Php\DTOs;

use CGrate\Php\Enums\ResponseCode;

/**
 * Data Transfer Object for balance inquiry response from CGrate API.
 */
final readonly class BalanceResponseDTO
{
    /**
     * Create a new balance response DTO.
     *
     * @param  ResponseCode  $responseCode  The response code from the API
     * @param  string  $responseMessage  The response message from the API
     * @param  float|null  $balance  The account balance, if successful
     */
    public function __construct(
        public ResponseCode $responseCode,
        public string $responseMessage,
        public ?float $balance,
    ) {
    }

    /**
     * Create a new balance response DTO from an API response.
     *
     * @param  array{responseCode:int,responseMessage:string,balance:?float}  
     * $response  The raw response from the API
     * @return  self  New balance response DTO instance
     */
    public static function fromResponse(array $response): self
    {
        return new self(
            responseCode: ResponseCode::fromValue($response['responseCode']),
            responseMessage: $response['responseMessage'],
            balance: isset($response['balance']) ? (float) $response['balance'] : null
        );
    }

    /**
     * Check if the response indicates a successful operation.
     *
     * @return  bool  True if the operation was successful
     */
    public function isSuccessful(): bool
    {
        return $this->responseCode->is(ResponseCode::SUCCESS);
    }

    /**
     * Show the balance with currency code
     * 
     * @param  bool $withCode  Display balance with currency code
     * 
     * @return  string  Formatted balance
     */
    public function displayBalance(bool $withCode = true): string
    {
        return ($withCode ? 'ZMW ' : '').number_format($this->balance ?? 0, 2);
    }

    /**
     * Format the balance
     * 
     * @return  float  Formatted balance
     */
    public function formatBalance(): float
    {
        return round($this->balance ?? 0, 2);
    }

    /**
     * Convert the DTO to an array.
     *
     * @return  array{responseCode:int,responseMessage:string,balance:float,displayBalance:string}
     */
    public function toArray(): array
    {
        return [
            'responseCode' => $this->responseCode->value,
            'responseMessage' => $this->responseCode->getDescription(),
            'balance' => $this->formatBalance(),
            'displayBalance' => $this->displayBalance(),
        ];
    }
}
