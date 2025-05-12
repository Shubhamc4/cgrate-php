<?php

declare(strict_types=1);

namespace CGrate\Php\Enums;

use BackedEnum;

/**
 * This enum defines all possible response codes that can be returned
 * by the CGrate api endpoints, along with their human-readable descriptions.
 */
enum ResponseCode: int
{
    case UNKNOWN = -1;
    case SUCCESS = 0;
    case INSUFFICIENT_BALANCE = 1;
    case GENERAL_ERROR = 6;
    case INVALID_MSISDN = 7;
    case PROCESS_DELAY = 8;
    case BALANCE_UPDATE_FAILED = 9;
    case BALANCE_RETRIEVAL_FAILED = 10;
    case ACCOUNT_ACTIVE = 31;
    case ACCOUNT_INACTIVE = 32;
    case ACCOUNT_SUSPENDED = 33;
    case ACCOUNT_CLOSED = 34;
    case PASSWORD_TRIES_EXCEEDED = 35;
    case INCORRECT_PASSWORD = 36;
    case ACCOUNT_NOT_EXIST = 37;
    case INSUFFICIENT_STOCK = 51;
    case INVALID_VOUCHER_REQUEST = 52;
    case INVALID_RECHARGE = 53;
    case INVALID_RECHARGE_DENOMINATION = 54;
    case VOUCHER_PROVIDER_CONTENT_FAILED = 55;
    case INVALID_VOUCHER_PROVIDER = 56;
    case INVALID_DISTRIBUTION_CHANNEL = 101;
    case USSD_TRANSACTION_NOT_AVAILABLE = 102;
    case TRANSACTION_REFERENCE_NOT_UNIQUE = 104;
    case INVALID_TRANSACTION_REFERENCE = 106;
    case RECONCILIATION_FAILED = 151;
    case NO_RECONCILIATION_FOUND = 152;
    case RECONCILIATION_FLAG_INCONSISTENT = 153;
    case ERROR_RECEIVING_RECONCILIATION_TOTAL = 154;
    case NO_TRANSACTIONS_FOUND = 213;

    /**
     * Create a ResponseCode enum from a string or integer value.
     *
     * @param  string|int  $value  The response code value
     * @return  self  New response code enum instance
     */
    public static function fromValue(string|int $value): self
    {
        return self::tryFrom((int) $value) ?? self::UNKNOWN;
    }

    /**
     * Get the description for a response code value.
     *
     * @param  string|int  $value  The response code value
     * @return  string  The human-readable description
     */
    public static function descriptionFromValue(string|int $value): string
    {
        return self::fromValue($value)->getDescription();
    }

    /**
     * Check if this response code matches another value.
     *
     * @param  null|int|string|BackedEnum  $value  The value to compare with
     * @return  bool  True if matches, false otherwise
     */
    public function is(null|int|string|BackedEnum $value): bool
    {
        if (! $value instanceof BackedEnum && $value !== null) {
            $value = self::fromValue($value);
        }

        return $this === $value;
    }

    /**
     * Get the human-readable description for this response code.
     */
    public function getDescription(): string
    {
        return match ($this) {
            self::UNKNOWN => 'Unknown response code',
            self::SUCCESS => 'Success',
            self::INSUFFICIENT_BALANCE => 'Insufficient balance',
            self::GENERAL_ERROR => 'General error',
            self::INVALID_MSISDN => 'Invalid MSISDN',
            self::PROCESS_DELAY => 'Process delay',
            self::BALANCE_UPDATE_FAILED => 'Balance update failed',
            self::BALANCE_RETRIEVAL_FAILED => 'Balance retrieval failed',
            self::ACCOUNT_ACTIVE => 'Account is active',
            self::ACCOUNT_INACTIVE => 'Account in inactive',
            self::ACCOUNT_SUSPENDED => 'Account is suspended',
            self::ACCOUNT_CLOSED => 'Account is closed',
            self::PASSWORD_TRIES_EXCEEDED => 'Password tries exceeded',
            self::INCORRECT_PASSWORD => 'Incorrect password',
            self::ACCOUNT_NOT_EXIST => 'Account does not exist',
            self::INSUFFICIENT_STOCK => 'Insufficient stock',
            self::INVALID_VOUCHER_REQUEST => 'Invalid voucher request',
            self::INVALID_RECHARGE => 'Invalid recharge',
            self::INVALID_RECHARGE_DENOMINATION => 'Invalid recharge denomination',
            self::VOUCHER_PROVIDER_CONTENT_FAILED => 'Voucher Provider content failed',
            self::INVALID_VOUCHER_PROVIDER => 'Invalid Voucher Provider',
            self::INVALID_DISTRIBUTION_CHANNEL => 'Invalid distribution channel',
            self::USSD_TRANSACTION_NOT_AVAILABLE => 'USSD transaction not available',
            self::TRANSACTION_REFERENCE_NOT_UNIQUE => 'Transaction reference not unique, please verify transaction through query transaction',
            self::INVALID_TRANSACTION_REFERENCE => 'Invalid transaction reference',
            self::RECONCILIATION_FAILED => 'Reconciliation failed',
            self::NO_RECONCILIATION_FOUND => 'No reconciliation found',
            self::RECONCILIATION_FLAG_INCONSISTENT => 'Reconciliation flag not consistent with count',
            self::ERROR_RECEIVING_RECONCILIATION_TOTAL => 'Error receiving reconciliation total',
            self::NO_TRANSACTIONS_FOUND => 'No transactions found',
        };
    }
}
