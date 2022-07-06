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

namespace CakeDC\Auth\Rbac;

/**
 * Provides additional context on the result of a permission match operation,
 * for example allows to attach a debug reason on the matched rule
 *
 * @package Auth\Rbac
 */
class PermissionMatchResult
{
    /**
     * @var bool
     */
    protected $_allowed;

    /**
     * @var string
     */
    protected $_reason;

    /**
     * PermissionMatchResult constructor.
     *
     * @param bool $allowed rule was matched, allowed value
     * @param string $reason reason to either allow or deny
     */
    public function __construct($allowed = false, $reason = '')
    {
        $this->_allowed = $allowed;
        $this->_reason = $reason;
    }

    /**
     * @param bool $allowed allowed value
     * @return \CakeDC\Auth\Rbac\PermissionMatchResult
     */
    public function setAllowed($allowed)
    {
        $this->_allowed = (bool)$allowed;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAllowed()
    {
        return $this->_allowed;
    }

    /**
     * @param string $reason reason
     * @return \CakeDC\Auth\Rbac\PermissionMatchResult
     */
    public function setReason($reason)
    {
        $this->_reason = (string)$reason;

        return $this;
    }

    /**
     * @return string
     */
    public function getReason()
    {
        return $this->_reason;
    }
}
