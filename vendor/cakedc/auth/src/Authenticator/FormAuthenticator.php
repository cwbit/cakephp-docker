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

use Authentication\Authenticator\AuthenticatorInterface;
use Authentication\Authenticator\FormAuthenticator as BaseFormAuthenticator;
use Authentication\Authenticator\Result;
use Authentication\Authenticator\ResultInterface;
use Authentication\Identifier\IdentifierInterface;
use Cake\Core\Configure;
use Cake\Core\InstanceConfigTrait;
use CakeDC\Auth\Traits\ReCaptchaTrait;
use Psr\Http\Message\ServerRequestInterface;

class FormAuthenticator implements AuthenticatorInterface
{
    use InstanceConfigTrait;
    use ReCaptchaTrait;

    /**
     * Failure due invalid reCAPTCHA
     */
    public const FAILURE_INVALID_RECAPTCHA = 'FAILURE_INVALID_RECAPTCHA';

    /**
     * @var \Authentication\Authenticator\AuthenticatorInterface
     */
    protected $baseAuthenticator;

    /**
     * Identifier or identifiers collection.
     *
     * @var \Authentication\Identifier\IdentifierInterface
     */
    protected $identifier;

    /**
     * Settings for base authenticator
     *
     * @var array
     */
    protected $_defaultConfig = [
        'keyCheckEnabledRecaptcha' => 'Users.reCaptcha.login',
    ];

    /**
     * Constructor
     *
     * @param \Authentication\Identifier\IdentifierInterface $identifier Identifier or identifiers collection.
     * @param array $config Configuration settings.
     */
    public function __construct(IdentifierInterface $identifier, array $config = [])
    {
        $this->identifier = $identifier;
        $this->setConfig($config);
    }

    /**
     * Gets the actual base authenticator
     *
     * @return \Authentication\Authenticator\AuthenticatorInterface
     */
    public function getBaseAuthenticator()
    {
        if ($this->baseAuthenticator === null) {
            $this->baseAuthenticator = $this->createBaseAuthenticator($this->identifier, $this->getConfig());
        }

        return $this->baseAuthenticator;
    }

    /**
     * Create the base authenticator
     *
     * @param \Authentication\Identifier\IdentifierInterface $identifier Identifier or identifiers collection.
     * @param array $config Configuration settings.
     * @return \Authentication\Authenticator\AuthenticatorInterface
     */
    protected function createBaseAuthenticator(IdentifierInterface $identifier, array $config = [])
    {
        unset($config['keyCheckEnabledRecaptcha']);
        if (!isset($config['baseClassName'])) {
            return new BaseFormAuthenticator($identifier, $config);
        }

        $className = $config['baseClassName'];
        unset($config['baseClassName']);
        if (!class_exists($className)) {
            throw new \InvalidArgumentException(__('Base class for FormAuthenticator {0} does not exist', $className));
        }

        return new $className($identifier, $config);
    }

    /**
     * Authenticates the identity contained in a request. Wrapper for Authentication\Authenticator\FormAuthenticator
     * to also check reCaptcha. Will use the `config.userModel`, and `config.fields`
     * to find POST data that is used to find a matching record in the `config.userModel`. Will return false if
     * there is no post data, either username or password is missing, or if the scope conditions have not been met.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The request that contains login information.
     * @return \Authentication\Authenticator\ResultInterface
     */
    public function authenticate(ServerRequestInterface $request): ResultInterface
    {
        $result = $this->getBaseAuthenticator()->authenticate($request);
        $checkKey = $this->getConfig('keyCheckEnabledRecaptcha');
        if (!Configure::read($checkKey) || in_array($result->getStatus(), [Result::FAILURE_OTHER, Result::FAILURE_CREDENTIALS_MISSING])) {
            return $result;
        }

        if ($this->validateReCaptchaFromRequest($request)) {
            return $result;
        }

        return new Result(null, self::FAILURE_INVALID_RECAPTCHA);
    }

    /**
     * Call base authenticator methods
     *
     * @param string $name base authentication method name
     * @param array $arguments used in base authenticator method
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return $this->getBaseAuthenticator()->$name(...$arguments);
    }
}
