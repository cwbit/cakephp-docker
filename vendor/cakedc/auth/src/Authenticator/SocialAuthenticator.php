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

use Authentication\Authenticator\AbstractAuthenticator;
use Authentication\Authenticator\Result;
use Authentication\Authenticator\ResultInterface;
use Authentication\UrlChecker\UrlCheckerTrait;
use Cake\Http\Exception\BadRequestException;
use Cake\Log\LogTrait;
use CakeDC\Auth\Identifier\SocialIdentifier;
use CakeDC\Auth\Social\MapUser;
use CakeDC\Auth\Social\Service\ServiceInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Social authenticator
 *
 * Authenticates an identity based on request attribute socialService (CakeDC\Auth\Social\Service\ServiceInterface)
 */
class SocialAuthenticator extends AbstractAuthenticator
{
    use LogTrait;
    use UrlCheckerTrait;

    public const SOCIAL_SERVICE_ATTRIBUTE = 'socialService';

    /**
     * Default config for this object.
     * - `fields` The fields to use to identify a user by.
     * - `loginUrl` Login URL or an array of URLs.
     * - `urlChecker` Url checker config.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    /**
     * Authenticates the identity contained in a request.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The request that contains login information.
     * @throws \Exception
     * @return \Authentication\Authenticator\ResultInterface
     */
    public function authenticate(ServerRequestInterface $request): ResultInterface
    {
        $service = $request->getAttribute(self::SOCIAL_SERVICE_ATTRIBUTE);
        if ($service === null) {
            return new Result(null, Result::FAILURE_CREDENTIALS_MISSING);
        }

        $rawData = $this->getRawData($request, $service);
        if (empty($rawData)) {
            return new Result(null, Result::FAILURE_IDENTITY_NOT_FOUND);
        }

        return $this->identify($rawData);
    }

    /**
     * Identify user with credential data
     *
     * @param array $rawData social user raw data
     * @return \Authentication\Authenticator\Result
     */
    protected function identify($rawData)
    {
        $user = $this->getIdentifier()->identify([SocialIdentifier::CREDENTIAL_KEY => $rawData]);
        if (!empty($user)) {
            return new Result($user, Result::SUCCESS);
        }

        return new Result(null, Result::FAILURE_IDENTITY_NOT_FOUND);
    }

    /**
     * Get user raw data from social provider
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request request object
     * @param \CakeDC\Auth\Social\Service\ServiceInterface $service social service
     * @throws \Exception
     * @return array|null
     */
    private function getRawData(ServerRequestInterface $request, ServiceInterface $service)
    {
        $rawData = null;
        try {
            $rawData = $service->getUser($request);
            $mapper = new MapUser();

            return $mapper($service, $rawData);
        } catch (\Exception $exception) {
            $list = [BadRequestException::class, \UnexpectedValueException::class];
            $this->throwIfNotInlist($exception, $list);
            $message = sprintf(
                "Error getting an access token / retrieving the authorized user's profile data. Error message: %s %s",
                $exception->getMessage(),
                $exception
            );
            $this->log($message);

            return null;
        }
    }

    /**
     * Throw the exception if not in the list
     *
     * @param \Exception $exception exception thrown
     * @param array $list list of allowed exception classes
     * @throws \Exception
     * @return void
     */
    private function throwIfNotInlist(\Exception $exception, array $list)
    {
        $className = get_class($exception);
        if (!in_array($className, $list)) {
            throw $exception;
        }
    }
}
