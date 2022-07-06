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

/**
 * Tumblr Mapper
 */
class Tumblr extends AbstractMapper
{
    /**
     * Map for provider fields
     *
     * @var array
     */
    protected $_mapFields = [
        'id' => 'uid',
        'username' => 'nickname',
        'full_name' => 'name',
        'first_name' => 'firstName',
        'last_name' => 'lastName',
        'email' => 'email',
        'avatar' => 'imageUrl',
        'bio' => 'extra.blogs.0.description',
        'validated' => 'validated',
        'link' => 'extra.blogs.0.url',
    ];

    /**
     * Get id property value
     *
     * @param mixed $rawData raw data
     * @return string
     */
    protected function _id($rawData)
    {
        return (string)crc32($rawData['nickname']);
    }
}
