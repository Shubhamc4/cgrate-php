<?php

declare(strict_types=1);

namespace CGrate\Php\DTOs;

use CGrate\Php\Enums\ResponseCode;

/**
 * Data Transfer Object for payment response from CGrate API.
 */
final readonly class PaymentResponseDTO
{
    /**
     * Create a new payment response DTO.
     *
     * @param  ResponseCode  $responseCode  The response code from the API indicating the status
     * @param  string  $responseMessage  The human-readable response message from the API
     * @param  string|null  $paymentID  The unique ID of the processed payment assigned by CGrate, if successful
     * @param  string|null  $customerMobile  Customer mobile number through which payment was processed
     * @param  string|null  $transactionReference  Client-provided transaction reference number
     * @param  float|null  $transactionAmount  Transaction amount that was processed
     */
    public function __construct(
        public ResponseCode $responseCode,
        public string $responseMessage,
        public ?string $paymentID = null,
        public ?string $customerMobile = null,
        public ?string $transactionReference = null,
        public ?float $transactionAmount = null,
    ) {
    }

    /**
     * Create a new payment response DTO from an API response.
     *
     * @param  array{responseCode:int,responseMessage:string,paymentID:?string,
     * customerMobile:?string,transactionReference:?string,transactionAmount:?float}  
     * $response  The raw response array from the API
     * @return  self  New payment response DTO instance
     */
    public static function fromResponse(array $response): self
    {
        return new self(
            responseCode: ResponseCode::fromValue($response['responseCode']),
            responseMessage: $response['responseMessage'],
            paymentID: $response['paymentID'] ?? null,
            customerMobile: $response['customerMobile'] ?? null,
            transactionReference: $response['transactionReference'] ?? null,
            transactionAmount: isset($response['transactionAmount']) ? (float) $response['transactionAmount'] : null,
        );
    }

    /**
     * Check if the response indicates a successful operation.
     *
     * @return  bool  True if the operation was successful (response code = 0)
     */
    public function isSuccessful(): bool
    {
        return $this->responseCode->is(ResponseCode::SUCCESS);
    }

    /**
     * Convert the DTO to an array.
     *
     * @return array{responseCode:int,responseMessage:string,paymentID:?string,
     * customerMobile:?string,transactionReference:?string,transactionAmount:?float} 
     */
    public function toArray(): array
    {
        return [
            'responseCode' => $this->responseCode->value,
            'responseMessage' => $this->responseMessage,
            'paymentID' => $this->paymentID,
            'customerMobile' => $this->customerMobile,
            'transactionReference' => $this->transactionReference,
            'transactionAmount' => $this->transactionAmount,
        ];
    }
}
