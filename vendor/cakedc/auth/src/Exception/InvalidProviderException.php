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

namespace CakeDC\Auth\Exception;

use Cake\Core\Exception\CakeException;

class InvalidProviderException extends CakeException
{
    protected $_messageTemplate = 'Invalid provider or missing class (%s)';
	
	/**
	 * @var int
	 */
    protected $code = 500;

    /**
     * InvalidProviderException constructor.
     *
     * @param array|string $message message
     * @param int $code code
     * @param null $previous previous
     */
    public function __construct($message, $code = 500, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
