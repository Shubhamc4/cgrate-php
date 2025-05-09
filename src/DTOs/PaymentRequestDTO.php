<?php

declare(strict_types=1);

namespace Cgrate\Php\DTOs;

/**
 * Data Transfer Object for payment request to Cgrate API.
 */
class PaymentRequestDTO
{
    private float $transactionAmount;
    private string $customerMobile;
    private string $paymentReference;

    /**
     * Create a new payment request DTO.
     *
     * @param float  $transactionAmount The amount of the transaction (must be positive)
     * @param string $customerMobile    The mobile number of the customer (should be in format 2609XXXXXXXX)
     * @param string $paymentReference  The unique reference for the payment (should be unique per transaction)
     */
    public function __construct(
        float $transactionAmount,
        string $customerMobile,
        string $paymentReference
    ) {
        $this->transactionAmount = $transactionAmount;
        $this->customerMobile = $customerMobile;
        $this->paymentReference = $paymentReference;
    }

    /**
     * Create a new payment request DTO.
     *
     * @param  float  $transactionAmount The amount of the transaction (must be positive)
     * @param  string $customerMobile    The mobile number of the customer (should be in format 2609XXXXXXXX)
     * @param  string $paymentReference  The unique reference for the payment (should be unique per transaction)
     * @return self New payment request DTO instance
     */
    public static function create(
        float $transactionAmount,
        string $customerMobile,
        string $paymentReference
    ): self {
        return new self(
            $transactionAmount,
            $customerMobile,
            $paymentReference
        );
    }

    /**
     * Convert the DTO to an array.
     *
     * @return array{transactionAmount: float, customerMobile: string, paymentReference: string}
     */
    public function toArray(): array
    {
        return [
            'transactionAmount' => $this->transactionAmount,
            'customerMobile' => $this->customerMobile,
            'paymentReference' => $this->paymentReference,
        ];
    }

    public function getTransactionAmount(): float
    {
        return $this->transactionAmount;
    }

    public function getCustomerMobile(): string
    {
        return $this->customerMobile;
    }

    public function getPaymentReference(): string
    {
        return $this->paymentReference;
    }
}
