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

namespace CakeDC\Auth\Authenticator;

use Authentication\Authenticator\CookieAuthenticator as BaseAuthenticator;
use Authentication\Authenticator\PersistenceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Cookie Authenticator
 *
 * Authenticates an identity based on a cookies data.
 */
class CookieAuthenticator extends BaseAuthenticator implements PersistenceInterface
{
    /**
     * Session key used to save tmp data
     */
    public const SESSION_DATA_KEY = 'CookieAuth';

    /**
     * @inheritDoc
     */
    public function persistIdentity(ServerRequestInterface $request, ResponseInterface $response, $identity): array
    {
        $field = $this->getConfig('rememberMeField');

        $bodyData = $request->getParsedBody();
        if (empty($bodyData)) {
            /**
             * @var \Cake\Http\Session $session
             */
            $session = $request->getAttribute('session');
            $bodyData = $session->read(self::SESSION_DATA_KEY);
            $session->delete(self::SESSION_DATA_KEY);
        }

        if (!$this->_checkUrl($request) || !is_array($bodyData) || empty($bodyData[$field])) {
            return [
                'request' => $request,
                'response' => $response,
            ];
        }

        $value = $this->_createToken($identity);
        $cookie = $this->_createCookie($value);

        return [
            'request' => $request,
            'response' => $response->withAddedHeader('Set-Cookie', $cookie->toHeaderValue()),
        ];
    }
}
