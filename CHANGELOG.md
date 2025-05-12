# Changelog

All notable changes to `cgrate-php` will be documented in this file.

## 2.0.1 - 2025-05-12

- Fixed namespace inconsistency in ResponseCode enum (Cgrate → CGrate)
- Corrected getEndpoint() method to check testMode flag instead of testEndpoint
- Enhanced validation in ConfigValidator to properly handle testMode
- Added proper null checks for balance value in displayBalance()
- Fixed parameter validation to better handle missing values
- Improved exception classes with better typing and detailed descriptions

## 2.0.0 - 2025-05-12

- Upgrade package to support php 8.2 and above

### Changed

- Converted ResponseCode class to a proper PHP 8.2 Enum (BackedEnum)
- Upgraded all DTOs to use PHP 8.2 readonly classes with public properties
- Renamed namespace from `Cgrate\Php` to `CGrate\Php` for consistency
- Made class inheritance more explicit with final classes where appropriate
- Added proper property promotion in class constructors
- Updated parameter naming for consistency across the codebase
- Enhanced error messages for better debugging
- Improved method signatures with more specific types

### Fixed

- Fixed incorrect endpoint resolution in getEndpoint() method
- Fixed PHP 8.2 compatibility issues throughout the codebase
- Standardized exception handling with more specific error messages

## 1.0.0 - 2025-05-09

### Added

- Initial release
- Support for CGrate payment service integration for core php
- Core functionality:
  - Get account balance
  - Process customer payments
  - Query transaction status
  - Reverse payments
- Comprehensive exception handling
- Data Transfer Objects for convenient API interaction
- Validation for payment requests
- WS-Security authentication for SOAP API
- Detailed documentation and usage examples
