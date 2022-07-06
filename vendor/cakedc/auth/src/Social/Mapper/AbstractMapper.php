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

namespace CakeDC\Auth\Social\Mapper;

use Cake\Utility\Hash;
use Cake\Utility\Inflector;

/**
 * AbstractMapper
 */
abstract class AbstractMapper
{
    /**
     * Provider Raw data
     *
     * @var array
     */
    protected $_rawData = [];

    /**
     * Map for provider fields
     *
     * @var array
     */
    protected $_mapFields = [];

    /**
     * Default Map for provider fields
     *
     * @var array
     */
    protected $_defaultMapFields = [
        'id' => 'id',
        'username' => 'username',
        'full_name' => 'name',
        'first_name' => 'first_name',
        'last_name' => 'last_name',
        'email' => 'email',
        'avatar' => 'avatar',
        'gender' => 'gender',
        'link' => 'link',
        'bio' => 'bio',
        'locale' => 'locale',
        'validated' => 'validated',
    ];

    /**
     * Constructor
     *
     * @param mixed $mapFields map fields
     */
    public function __construct($mapFields = null)
    {
        if (!is_null($mapFields)) {
            $this->_mapFields = $mapFields;
        }
        $this->_mapFields = array_merge($this->_defaultMapFields, $this->_mapFields);
    }

    /**
     * Invoke method
     *
     * @param mixed $rawData raw data
     * @return mixed
     */
    public function __invoke($rawData)
    {
        return $this->_map($rawData);
    }

    /**
     * If email is present the user is validated
     *
     * @param mixed $rawData raw data
     * @return bool
     */
    protected function _validated($rawData)
    {
        $email = Hash::get($rawData, $this->_mapFields['email']);

        return !empty($email);
    }

    /**
     * Maps raw data using mapFields
     *
     * @param mixed $rawData raw data
     * @return mixed
     */
    protected function _map($rawData)
    {
        $result = [];
        collection($this->_mapFields)->each(function (string $mappedField, string $field) use (&$result, $rawData): void {
            $value = Hash::get($rawData, $mappedField);
            $function = '_' . Inflector::camelize($field);
            if (method_exists($this, $function)) {
                $value = $this->{$function}($rawData);
            }
            $result[$field] = $value;
        });
        $token = $rawData['token'] ?? null;
        if (empty($token) || !is_array($token) && !$token instanceof \League\OAuth2\Client\Token\AccessToken) {
            return false;
        }

        $result['credentials'] = [
            'token' => is_array($token) ? ($token['accessToken'] ?? null) : $token->getToken(),
            'secret' => is_array($token) ? ($token['tokenSecret'] ?? null) : null,
            'expires' => is_array($token) ? ($token['expires'] ?? null) : $token->getExpires(),
        ];
        $result['raw'] = $rawData;

        return $result;
    }
}
