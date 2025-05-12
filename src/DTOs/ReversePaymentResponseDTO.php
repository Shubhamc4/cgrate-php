<?php

declare(strict_types=1);

namespace CGrate\Php\DTOs;

use CGrate\Php\Enums\ResponseCode;

/**
 * Data Transfer Object for payment reversal response from CGrate API.
 */
final readonly class ReversePaymentResponseDTO
{
    /**
     * Create a new reverse payment response DTO.
     *
     * @param  ResponseCode  $responseCode  The response code from the API
     * @param  string  $responseMessage  The response message from the API
     */
    public function __construct(
        public ResponseCode $responseCode,
        public string $responseMessage,
        public string $transactionReference,
    ) {
    }

    /**
     * Create a new reverse payment response DTO from an API response.
     *
     * @param  array{responseCode:int,responseMessage:string,transactionReference:string}
     * $response  The raw response from the API
     * @return  self  New reverse payment response DTO instance
     */
    public static function fromResponse(array $response): self
    {
        return new self(
            responseCode: ResponseCode::fromValue($response['responseCode']),
            responseMessage: $response['responseMessage'],
            transactionReference: $response['transactionReference'],
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
     * Convert the DTO to an array.
     *
     * @return  array{responseCode:int,responseMessage:string,transactionReference:string}
     */
    public function toArray(): array
    {
        return [
            'responseCode' => $this->responseCode->value,
            'responseMessage' => $this->responseMessage,
            'transactionReference' => $this->transactionReference,
        ];
    }
}
