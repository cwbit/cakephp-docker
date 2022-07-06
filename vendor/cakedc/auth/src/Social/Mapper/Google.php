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
 * Google Mapper
 */
class Google extends AbstractMapper
{
    /**
     * Map for provider fields
     *
     * @var array
     */
    protected $_mapFields = [
        'id' => 'sub',
        'avatar' => 'picture',
        'full_name' => 'name',
        'email' => 'email',
        'first_name' => 'given_name',
        'last_name' => 'family_name',
        'bio' => 'aboutMe',
        'link' => 'profile',
    ];

    /**
     * Get link property value
     *
     * @param mixed $rawData raw data
     * @return string
     */
    protected function _link($rawData)
    {
        return Hash::get($rawData, $this->_mapFields['link']) ?: '#';
    }
}
