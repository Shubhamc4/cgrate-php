<?php

declare(strict_types=1);

namespace Cgrate\Php\Validation;

use Cgrate\Php\Config\CgrateConfig;
use Cgrate\Php\Exceptions\ValidationException;

/**
 * Validator for CGrate configuration.
 *
 * Validates configuration values to ensure all required settings
 * are properly configured before initializing the API client.
 */
class ConfigValidator
{
    private static array $requiredKeys = [
        'username',
        'password',
        'endpoint',
        'test_endpoint',
    ];

    /**
     * Validate CGrate configuration.
     *
     * @param  array $config The configuration array to validate
     * @return array{username: string, password: string,
     * endpoint: string, test_endpoint: string, options: array}
     * The validated and normalized configuration
     * @throws \Cgrate\Php\Exceptions\ValidationException If configuration is invalid
     */
    public static function validate(array $config): array
    {
        $errors = [];

        foreach (self::$requiredKeys as $key) {
            if (!isset($config[$key]) || empty($config[$key])) {
                $errors[$key] = "The '{$key}' configuration value is required";
            }
        }

        if (!filter_var($config['endpoint'], FILTER_VALIDATE_URL)) {
            $errors['endpoint'] = 'The endpoint must be a valid URL';
        }

        if (!filter_var($config['test_endpoint'], FILTER_VALIDATE_URL)) {
            $errors['test_endpoint'] = 'The test_endpoint must be a valid URL';
        }

        if (!isset($config['test_mode'])) {
            $config['test_mode'] = false;
        }

        if (!isset($config['options']) || !is_array($config['options'])) {
            $config['options'] = CgrateConfig::getDefaultOptions($config['test_mode']);
        } else {
            $config['options'] = array_merge(CgrateConfig::getDefaultOptions($config['test_mode']), $config['options']);
        }

        if (!empty($errors)) {
            throw ValidationException::withErrors($errors);
        }

        return $config;
    }
}
