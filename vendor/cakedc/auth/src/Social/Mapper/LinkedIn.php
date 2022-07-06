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

class LinkedIn extends AbstractMapper
{
    /**
     * Map for provider fields
     *
     * @var array
     */
    protected $_mapFields = [
        'avatar' => 'pictureUrl',
        'first_name' => 'firstName',
        'last_name' => 'lastName',
        'email' => 'emailAddress',
        'bio' => 'headline',
        'link' => 'publicProfileUrl',
    ];
}
