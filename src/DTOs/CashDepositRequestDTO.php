<?php

declare(strict_types=1);

namespace CGrate\Php\DTOs;

/**
 * Data Transfer Object for cash deposit request to CGrate API.
 */
final readonly class CashDepositRequestDTO
{
    /**
     * Create a new cash deposit request DTO.
     *
     * @param  float  $transactionAmount  The amount of the transaction (must be positive)
     * @param  string  $customerAccount  The contact number of the customer (should be in format 260XXXXXXXXX)
     * @param  string  $issuerName  The customer account issuer name
     * @param  string  $depositorReference  The unique reference for the cash deposit (should be unique per transaction)
     */
    public function __construct(
        public float $transactionAmount,
        public string $customerAccount,
        public string $issuerName,
        public string $depositorReference,
    ) {
    }

    /**
     * Create a new cash deposit request DTO.
     *
     * @param  float  $transactionAmount  The amount of the transaction (must be positive)
     * @param  string  $customerAccount  The contact number of the customer (should be in format 260XXXXXXXXX)
     * @param  string  $issuerName  The customer account issuer name
     * @param  string  $depositorReference  The unique reference for the cash deposit (should be unique per transaction)
     * @return  self  New cash deposit request DTO instance
     */
    public static function create(
        float $transactionAmount,
        string $customerAccount,
        string $issuerName,
        string $depositorReference,
    ): self {
        return new self(
            transactionAmount: $transactionAmount,
            customerAccount: $customerAccount,
            issuerName: $issuerName,
            depositorReference: $depositorReference,
        );
    }

    /**
     * Convert the DTO to an array.
     *
     * @return  array{transactionAmount:float,customerAccount:string,issuerName:string,depositorReference:string}
     */
    public function toArray(): array
    {
        return [
            'transactionAmount' => $this->transactionAmount,
            'customerAccount' => $this->customerAccount,
            'issuerName' => $this->issuerName,
            'depositorReference' => $this->depositorReference,
        ];
    }
}
