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

/**
 * Cognito Mapper
 */
class Cognito extends AbstractMapper
{
    /**
     * Map for provider fields
     *
     * @var array
     */
    protected $_mapFields = [
        'id' => 'sub',
        'zoneinfo' => 'zoneinfo',
        'link' => 'website',
        'bio' => 'profile',
        'first_name' => 'given_name',
        'avatar' => 'picture',
        'last_name' => 'family_name',
    ];

    /**
     * Get link property value
     *
     * @param mixed $rawData raw data
     * @return string
     */
    protected function _link($rawData)
    {
        return Hash::get($rawData, $this->_mapFields['link'], '#');
    }

    /**
     *  Get first_name property value
     *
     * @param mixed $rawData raw data
     * @return mixed
     */
    protected function _firstName($rawData)
    {
        return $this->getNameValue($rawData, 'first_name', 0);
    }

    /**
     * Get last_name property value
     *
     * @param mixed $rawData raw data
     * @return mixed
     */
    protected function _lastName($rawData)
    {
        return $this->getNameValue($rawData, 'last_name', 1);
    }

    /**
     * Helper function to get a name portion(fist_name, last_name) value
     *
     * @param mixed $rawData raw data
     * @param string $field Name field (first_name, last_name)
     * @param int $keyInName key of string part in name field after exploder(' ', $value)
     * @return string|null
     */
    private function getNameValue($rawData, $field, $keyInName)
    {
        $value = Hash::get($rawData, $this->_mapFields[$field]);
        if ($value === null) {
            $names = explode(' ', Hash::get($rawData, 'name', ''));

            return $names[$keyInName] ?? null;
        }

        return (string)$value;
    }
}
