<?php
declare(strict_types=1);

/**
 * Copyright 2010 - 2019, Cake Development Corporation (https://www.cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2010 - 2019, Cake Development Corporation (https://www.cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace CakeDC\Auth\Social;

use Cake\Core\Configure;
use Cake\Utility\Hash;
use CakeDC\Auth\Exception\InvalidProviderException;
use CakeDC\Auth\Exception\InvalidSettingsException;

class ProviderConfig
{
    /**
     * @var array
     */
    protected $providers;

    /**
     * ProviderConfig constructor.
     *
     * @param array $config additional data
     */
    public function __construct($config = [])
    {
        $oauthConfig = Configure::read('OAuth');

        $providers = [];
        foreach ($oauthConfig['providers'] as $provider => $options) {
            if ($this->_isProviderEnabled($options)) {
                $providers[$provider] = $options;
            }
        }
        $oauthConfig['providers'] = $providers;

        $this->providers = $this->normalizeConfig(Hash::merge($config, $oauthConfig))['providers'];
    }

    /**
     * Normalizes providers' configuration.
     *
     * @param array $config Array of config to normalize.
     * @return array
     * @throws \Exception
     */
    public function normalizeConfig(array $config)
    {
        if (!empty($config['providers'])) {
            array_walk($config['providers'], [$this, '_normalizeConfig'], $config);
        }

        return $config;
    }

    /**
     * Callback to loop through config values.
     *
     * @param array $config Configuration.
     * @param string $alias Provider's alias (key) in configuration.
     * @param array $parent Parent configuration.
     * @return void
     */
    protected function _normalizeConfig(&$config, $alias, $parent)
    {
        unset($parent['providers']);

        $defaults = [
                'className' => null,
                'service' => null,
                'mapper' => null,
                'authParams' => [],
                'options' => [],
                'collaborators' => [],
                'signature' => null,
                'mapFields' => [],
            ] + $parent;

        $config = array_intersect_key($config, $defaults);
        $config += $defaults;

        array_walk($config, [$this, '_validateConfig']);

        foreach (['options', 'collaborators', 'signature'] as $key) {
            if (empty($parent[$key]) || empty($config[$key])) {
                continue;
            }

            $config[$key] = array_merge($parent[$key], $config[$key]);
        }
    }

    /**
     * Validates the configuration.
     *
     * @param mixed $value Value.
     * @param string $key Key.
     * @return void
     * @throws \CakeDC\Auth\Exception\InvalidProviderException
     * @throws \CakeDC\Auth\Exception\InvalidSettingsException
     */
    protected function _validateConfig(&$value, $key)
    {
        if (in_array($key, ['className', 'service', 'mapper'], true) && !is_object($value) && !class_exists($value)) {
            throw new InvalidProviderException([$value]);
        } elseif (!is_array($value) && in_array($key, ['options', 'collaborators'])) {
            throw new InvalidSettingsException([$key]);
        }
    }

    /**
     * Returns when a provider has been enabled.
     *
     * @param array $options array of options by provider
     * @return bool
     */
    protected function _isProviderEnabled($options)
    {
        return !empty($options['options']['redirectUri']) && !empty($options['options']['clientId']) &&
            !empty($options['options']['clientSecret']);
    }

    /**
     * Get provider config
     *
     * @param string $alias for provider
     * @return array
     */
    public function getConfig($alias)
    {
        return Hash::get($this->providers, $alias, []);
    }
}
