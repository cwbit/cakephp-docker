<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         1.0.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Authorization\Middleware\UnauthorizedHandler;

use Authorization\Exception\MissingIdentityException;
use Cake\Routing\Router;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;

/**
 * This handler will redirect the response if one of configured exception classes is encountered.
 *
 * CakePHP Router compatible array URL syntax is supported.
 */
class CakeRedirectHandler extends RedirectHandler
{
    /**
     * @inheritDoc
     */
    protected $defaultOptions = [
        'exceptions' => [
            MissingIdentityException::class,
        ],
        'url' => [
            'controller' => 'Users',
            'action' => 'login',
        ],
        'queryParam' => 'redirect',
        'statusCode' => 302,
    ];

    /**
     * Constructor.
     *
     * @throws \RuntimeException When `Cake\Routing\Router` class cannot be found.
     */
    public function __construct()
    {
        if (!class_exists(Router::class)) {
            $message = sprintf(
                'Class `%s` does not exist. ' .
                'Make sure you are using a full CakePHP framework ' .
                'and have autoloading configured properly.',
                Router::class
            );
            throw new RuntimeException($message);
        }
    }

    /**
     * @inheritDoc
     */
    protected function getUrl(ServerRequestInterface $request, array $options): string
    {
        $url = $options['url'];
        if ($options['queryParam'] !== null) {
            $uri = $request->getUri();
            $redirect = $uri->getPath();
            if ($uri->getQuery()) {
                $redirect .= '?' . $uri->getQuery();
            }

            $url['?'][$options['queryParam']] = $redirect;
        }

        return Router::url($url);
    }
}
