<?php

declare(strict_types=1);

namespace CGrate\Php\DTOs;

use CGrate\Php\Enums\ResponseCode;

/**
 * Data Transfer Object for cash payment response from CGrate API.
 */
final readonly class CashDepositResponseDTO
{
    /**
     * Create a new cash payment response DTO.
     *
     * @param  ResponseCode  $responseCode  The response code from the API
     * @param  string  $responseMessage  The response message from the API
     */
    public function __construct(
        public ResponseCode $responseCode,
        public string $responseMessage,
        public string $depositorReference,
    ) {
    }

    /**
     * Create a new cash payment response DTO from an API response.
     *
     * @param  array{responseCode:int,responseMessage:string,depositorReference:string}
     * $response  The raw response from the API
     * @return  self  New cash payment response DTO instance
     */
    public static function fromResponse(array $response): self
    {
        return new self(
            responseCode: ResponseCode::fromValue($response['responseCode']),
            responseMessage: $response['responseMessage'],
            depositorReference: $response['depositorReference'],
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
     * @return  array{responseCode:int,responseMessage:string,depositorReference:string}
     */
    public function toArray(): array
    {
        return [
            'responseCode' => $this->responseCode->value,
            'responseMessage' => $this->responseMessage,
            'depositorReference' => $this->depositorReference,
        ];
    }
}
