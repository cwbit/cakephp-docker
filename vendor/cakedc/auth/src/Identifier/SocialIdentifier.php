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

namespace CakeDC\Auth\Identifier;

use Authentication\Identifier\AbstractIdentifier;
use Authentication\Identifier\Resolver\ResolverAwareTrait;

class SocialIdentifier extends AbstractIdentifier
{
    use ResolverAwareTrait;

    public const CREDENTIAL_KEY = 'socialAuthUser';

    protected $_defaultConfig = [
        'resolver' => 'Authentication.Orm',
    ];

    /**
     * Identifies an user or service by the passed credentials
     *
     * @param array $credentials Authentication credentials
     * @return \ArrayAccess|array|null
     */
    public function identify(array $credentials)
    {
        if (!isset($credentials[self::CREDENTIAL_KEY]['email'])) {
            return null;
        }

        return $this->getResolver()->find([
            'email' => $credentials[self::CREDENTIAL_KEY]['email'],
        ]);
    }
}
