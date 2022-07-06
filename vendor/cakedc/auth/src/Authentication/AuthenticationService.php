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

namespace CakeDC\Auth\Authentication;

use Authentication\AuthenticationService as BaseService;
use Authentication\Authenticator\Result;
use Authentication\Authenticator\ResultInterface;
use Authentication\Authenticator\StatelessInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;

class AuthenticationService extends BaseService
{
    public const NEED_TWO_FACTOR_VERIFY = 'NEED_TWO_FACTOR_VERIFY';

    public const TWO_FACTOR_VERIFY_SESSION_KEY = 'temporarySession';

    public const U2F_SESSION_KEY = 'U2f.User';

    public const NEED_U2F_VERIFY = 'NEED_U2F_VERIFY';

    public const NEED_WEBAUTHN_2FA_VERIFY = 'NEED_WEBAUTHN2FA_VERIFY';

    public const WEBAUTHN_2FA_SESSION_KEY = 'Webauthn2fa.User';

    /**
     * All failures authenticators
     *
     * @var \CakeDC\Auth\Authentication\Failure[]
     */
    protected $failures = [];

    /**
     * Proceed to google verify action after a valid result result
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The request.
     * @param \Authentication\Authenticator\ResultInterface $result The original result
     * @return \Authentication\Authenticator\ResultInterface The result object.
     */
    protected function proceedToGoogleVerify(ServerRequestInterface $request, ResultInterface $result)
    {
        /**
         * @var \Cake\Http\Session $session
         */
        $session = $request->getAttribute('session');
        $session->write(self::TWO_FACTOR_VERIFY_SESSION_KEY, $result->getData());
        $result = new Result(null, self::NEED_TWO_FACTOR_VERIFY);
        $this->_successfulAuthenticator = null;

        return $this->_result = $result;
    }
    /**
     * Proceed to webauthn2fa flow after a valid result result
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request response to manipulate
     * @param \Authentication\Authenticator\ResultInterface $result valid result
     * @return \Authentication\Authenticator\ResultInterface with result, request and response keys
     */
    protected function proceedToWebauthn2fa(ServerRequestInterface $request, ResultInterface $result)
    {
        /**
         * @var \Cake\Http\Session $session
         */
        $session = $request->getAttribute('session');
        $session->write(self::WEBAUTHN_2FA_SESSION_KEY, $result->getData());
        $result = new Result(null, self::NEED_WEBAUTHN_2FA_VERIFY);
        $this->_successfulAuthenticator = null;

        return $this->_result = $result;
    }

    /**
     * Proceed to U2f flow after a valid result result
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request response to manipulate
     * @param \Authentication\Authenticator\ResultInterface $result valid result
     * @return \Authentication\Authenticator\ResultInterface with result, request and response keys
     */
    protected function proceedToU2f(ServerRequestInterface $request, ResultInterface $result)
    {
        /**
         * @var \Cake\Http\Session $session
         */
        $session = $request->getAttribute('session');
        $session->write(self::U2F_SESSION_KEY, $result->getData());
        $result = new Result(null, self::NEED_U2F_VERIFY);
        $this->_successfulAuthenticator = null;

        return $this->_result = $result;
    }

    /**
     * Get the configured one-time password authentication checker
     *
     * @return \CakeDC\Auth\Authentication\OneTimePasswordAuthenticationCheckerInterface
     */
    protected function getOneTimePasswordAuthenticationChecker()
    {
        return (new OneTimePasswordAuthenticationCheckerFactory())->build();
    }

    /**
     * Get the configured u2f authentication checker
     *
     * @return \CakeDC\Auth\Authentication\Webauthn2FAuthenticationCheckerInterface
     */
    protected function getWebauthn2fAuthenticationChecker()
    {
        return (new Webauthn2fAuthenticationCheckerFactory())->build();
    }

    /**
     * Get the configured u2f authentication checker
     *
     * @return \CakeDC\Auth\Authentication\Webauthn2FAuthenticationCheckerInterface
     */
    protected function getU2fAuthenticationChecker()
    {
        return (new U2fAuthenticationCheckerFactory())->build();
    }

    /**
     * {@inheritDoc}
     *
     * @throws \RuntimeException Throws a runtime exception when no authenticators are loaded.
     */
    public function authenticate(ServerRequestInterface $request): ResultInterface
    {
        if ($this->authenticators()->isEmpty()) {
            throw new RuntimeException(
                'No authenticators loaded. You need to load at least one authenticator.'
            );
        }

        $result = null;
        foreach ($this->authenticators() as $authenticator) {
            $result = $authenticator->authenticate($request);
            if ($result->isValid()) {
                $skipTwoFactorVerify = $authenticator->getConfig('skipTwoFactorVerify');
                $userData = $result->getData()->toArray();
                $webauthn2faChecker = $this->getWebauthn2fAuthenticationChecker();
                if ($skipTwoFactorVerify !== true && $webauthn2faChecker->isRequired($userData)) {
                    return $this->proceedToWebauthn2fa($request, $result);
                }

                $u2fCheck = $this->getU2fAuthenticationChecker();
                if ($skipTwoFactorVerify !== true && $u2fCheck->isRequired($userData)) {
                    return $this->proceedToU2f($request, $result);
                }

                $otpCheck = $this->getOneTimePasswordAuthenticationChecker();
                if ($skipTwoFactorVerify !== true && $otpCheck->isRequired($userData)) {
                    return $this->proceedToGoogleVerify($request, $result);
                }

                $this->_successfulAuthenticator = $authenticator;
                $this->_result = $result;

                return $this->_result = $result;
            } else {
                $this->failures[] = new Failure($authenticator, $result);
            }

            if ($authenticator instanceof StatelessInterface) {
                $authenticator->unauthorizedChallenge($request);
            }
        }

        $this->_successfulAuthenticator = null;

        return $this->_result = $result;
    }

    /**
     * Get list the list of failures processed
     *
     * @return \CakeDC\Auth\Authentication\Failure[]
     */
    public function getFailures()
    {
        return $this->failures;
    }
}
