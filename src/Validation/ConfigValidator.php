<?php

declare(strict_types=1);

namespace CGrate\Php\Validation;

use CGrate\Php\Config\CGrateConfig;
use CGrate\Php\Exceptions\ValidationException;

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
        'testEndpoint',
    ];

    /**
     * Validate CGrate configuration.
     *
     * @param  array $config The configuration array to validate
     * @return  array{username:string,password:string,endpoint:string,testEndpoint:string,options:array}
     * The validated and normalized configuration
     * @throws  \CGrate\Php\Exceptions\ValidationException  If configuration is invalid
     */
    public static function validate(array $config): array
    {
        $errors = [];

        foreach (self::$requiredKeys as $key) {
            if (! isset($config[$key]) || empty($config[$key])) {
                $errors[$key] = "The '{$key}' configuration value is required";
            }
        }

        if (! filter_var($config['endpoint'], FILTER_VALIDATE_URL)) {
            $errors['endpoint'] = 'The endpoint must be a valid URL';
        }

        if (! filter_var($config['testEndpoint'], FILTER_VALIDATE_URL)) {
            $errors['testEndpoint'] = 'The test endpoint must be a valid URL';
        }

        if (! isset($config['testMode'])) {
            $config['testMode'] = false;
        }

        if (! isset($config['options']) || ! is_array($config['options'])) {
            $config['options'] = CGrateConfig::getDefaultOptions($config['testMode']);
        } else {
            $config['options'] = array_merge(CGrateConfig::getDefaultOptions($config['testMode']), $config['options']);
        }

        if (! empty($errors)) {
            throw ValidationException::withErrors($errors);
        }

        return $config;
    }
}
