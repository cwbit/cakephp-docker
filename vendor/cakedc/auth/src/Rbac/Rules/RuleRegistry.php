<?php
declare(strict_types=1);

/*
 * Copyright 2010 - 2021, Cake Development Corporation (https://www.cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2010 - 2021, Cake Development Corporation (https://www.cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace CakeDC\Auth\Rbac\Rules;

/**
 * Static rule registry to allow reusing rule instances in Rbac permissions
 */
class RuleRegistry
{
    /**
     * Rule instances array
     *
     * @var array
     */
    protected static $rules = [];

    /**
     * Get a new Rule instance by class, construct a new instance if not found
     *
     * @param string $class Rule class name
     * @param array|null $config options
     * @return \CakeDC\Auth\Rbac\Rules\Rule
     */
    public static function get(string $class, ?array $config = []): Rule
    {
        if (!class_exists($class)) {
            throw new \BadMethodCallException(sprintf('Unknown rule class %s', $class));
        }
        $key = $class . md5(\json_encode($config));
        if (!isset(static::$rules[$key])) {
            $ruleInstance = new $class($config);
            static::$rules[$key] = $ruleInstance;
        }

        return static::$rules[$key];
    }

    /**
     * Return all the rules as array
     *
     * @return array
     */
    public static function toArray(): array
    {
        return static::$rules;
    }

    /**
     * Clear the registry
     */
    public static function clear(): void
    {
        static::$rules = [];
    }
}
