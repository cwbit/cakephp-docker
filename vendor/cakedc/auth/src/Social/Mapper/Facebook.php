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
 * Facebook Mapper
 */
class Facebook extends AbstractMapper
{
    /**
     * Url constants
     */
    public const FB_GRAPH_BASE_URL = 'https://graph.facebook.com/';

    /**
     * Map for provider fields
     *
     * @var array
     */
    protected $_mapFields = [
        'full_name' => 'name',
    ];

    /**
     * Get avatar url
     *
     * @param mixed $rawData raw data
     * @return string
     */
    protected function _avatar($rawData)
    {
        return self::FB_GRAPH_BASE_URL . ($rawData['id'] ?? '') . '/picture?type=large';
    }

    /**
     * Get link property value
     *
     * @param mixed $rawData raw data
     * @return string
     */
    protected function _link($rawData)
    {
        return $rawData['link'] ?? null ?: '#';
    }
}
