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

namespace CakeDC\Auth\Middleware;

use Cake\Core\Configure;
use Cake\Http\Response;
use Cake\Routing\Router;
use CakeDC\Auth\Authentication\AuthenticationService;
use CakeDC\Auth\Authenticator\CookieAuthenticator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class TwoFactorMiddleware implements MiddlewareInterface
{
    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $service = $request->getAttribute('authentication');
        $status = $service->getResult() ? $service->getResult()->getStatus() : null;
        switch ($status) {
            case AuthenticationService::NEED_TWO_FACTOR_VERIFY:
                $url = Configure::read('OneTimePasswordAuthenticator.verifyAction');
                break;
            case AuthenticationService::NEED_U2F_VERIFY:
                $url = Configure::read('U2f.startAction');
                break;
            case AuthenticationService::NEED_WEBAUTHN_2FA_VERIFY:
                $url = Configure::read('Webauthn2fa.startAction');
                break;
            default:
                return $handler->handle($request);
        }
        /**
         * @var \Cake\Http\Session $session
         */
        $session = $request->getAttribute('session');
        $data = $request->getParsedBody();
        $data = is_array($data) ? $data : [];
        $session->write(CookieAuthenticator::SESSION_DATA_KEY, [
            'remember_me' => $data['remember_me'] ?? null,
        ]);
        $url = array_merge($url, [
            '?' => $request->getQueryParams(),
        ]);
        $url = Router::url($url);

        return (new Response())
            ->withHeader('Location', $url)
            ->withStatus(302);
    }
}
