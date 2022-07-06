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

use Authentication\UrlChecker\UrlCheckerTrait;
use Cake\Core\InstanceConfigTrait;
use Cake\Http\Response;
use Cake\Log\LogTrait;
use CakeDC\Auth\Authenticator\SocialAuthenticator;
use CakeDC\Auth\Social\Service\ServiceFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class SocialAuthMiddleware implements MiddlewareInterface
{
    use InstanceConfigTrait;
    use LogTrait;
    use UrlCheckerTrait;

    /**
     * The default config.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'loginUrl' => false,
        'urlChecker' => 'Authentication.Default',
    ];

    /**
     * SocialAuthMiddleware constructor.
     *
     * @param array $config optional configuration
     */
    public function __construct(array $config = [])
    {
        $this->setConfig($config);
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$this->checkUrl($request)) {
            return $handler->handle($request);
        }

        $service = (new ServiceFactory())->createFromRequest($request);
        if (!$service->isGetUserStep($request)) {
            return (new Response())
                ->withLocation($service->getAuthorizationUrl($request));
        }
        $request = $request->withAttribute(SocialAuthenticator::SOCIAL_SERVICE_ATTRIBUTE, $service);

        return $handler->handle($request);
    }

    /**
     * Check if is target url
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The request.
     * @return bool
     */
    protected function checkUrl(ServerRequestInterface $request)
    {
        return $this->_getUrlChecker()->check($request, $this->getConfig('loginUrl'));
    }
}
