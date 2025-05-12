<?php

declare(strict_types=1);

namespace CGrate\Php\DTOs;

/**
 * Data Transfer Object for payment request to CGrate API.
 */
final readonly class PaymentRequestDTO
{
    /**
     * Create a new payment request DTO.
     *
     * @param  float  $transactionAmount  The amount of the transaction (must be positive)
     * @param  string  $customerMobile  The mobile number of the customer (should be in format 2609XXXXXXXX)
     * @param  string  $paymentReference  The unique reference for the payment (should be unique per transaction)
     */
    public function __construct(
        public readonly float $transactionAmount,
        public readonly string $customerMobile,
        public readonly string $paymentReference,
    ) {
    }

    /**
     * Create a new payment request DTO.
     *
     * @param  float  $transactionAmount  The amount of the transaction (must be positive)
     * @param  string  $customerMobile  The mobile number of the customer (should be in format 2609XXXXXXXX)
     * @param  string  $paymentReference  The unique reference for the payment (should be unique per transaction)
     * @return  self  New payment request DTO instance
     */
    public static function create(
        float $transactionAmount,
        string $customerMobile,
        string $paymentReference,
    ): self {
        return new self(
            transactionAmount: $transactionAmount,
            customerMobile: $customerMobile,
            paymentReference: $paymentReference,
        );
    }

    /**
     * Convert the DTO to an array.
     *
     * @return  array{transactionAmount:float,customerMobile:string,paymentReference:string}
     */
    public function toArray(): array
    {
        return [
            'transactionAmount' => $this->transactionAmount,
            'customerMobile' => $this->customerMobile,
            'paymentReference' => $this->paymentReference,
        ];
    }
}
