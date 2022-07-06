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

use Authentication\Authenticator\AuthenticatorInterface;
use Authentication\Authenticator\ResultInterface;

class Failure implements FailureInterface
{
    /**
     * @var \Authentication\Authenticator\AuthenticatorInterface
     */
    protected $authenticator;

    /**
     * @var \Authentication\Authenticator\ResultInterface
     */
    protected $result;

    /**
     * Constructor.
     *
     * @param \Authentication\Authenticator\AuthenticatorInterface $authenticator Authenticator.
     * @param \Authentication\Authenticator\ResultInterface $result Result.
     */
    public function __construct(AuthenticatorInterface $authenticator, ResultInterface $result)
    {
        $this->authenticator = $authenticator;
        $this->result = $result;
    }

    /**
     * Returns failed authenticator.
     *
     * @return \Authentication\Authenticator\AuthenticatorInterface
     */
    public function getAuthenticator()
    {
        return $this->authenticator;
    }

    /**
     * Returns failed result.
     *
     * @return \Authentication\Authenticator\ResultInterface
     */
    public function getResult()
    {
        return $this->result;
    }
}
