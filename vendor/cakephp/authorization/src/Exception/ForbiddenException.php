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
namespace Authorization\Exception;

use Authorization\Policy\ResultInterface;
use Throwable;

class ForbiddenException extends Exception
{
    /**
     * @inheritDoc
     */
    protected $_defaultCode = 403;

    /**
     * @inheritDoc
     */
    protected $_messageTemplate = 'Identity is not authorized to perform `%s` on `%s`.';

    /**
     * Policy check result.
     *
     * @var \Authorization\Policy\ResultInterface|null
     */
    protected $result;

    /**
     * Constructor
     *
     * @param \Authorization\Policy\ResultInterface|null $result Policy check result.
     * @param string|array $message Either the string of the error message, or an array of attributes
     *   that are made available in the view, and sprintf()'d into Exception::$_messageTemplate
     * @param int|null $code The code of the error, is also the HTTP status code for the error.
     * @param \Throwable|null $previous the previous exception.
     */
    public function __construct(
        ?ResultInterface $result = null,
        $message = '',
        ?int $code = null,
        ?Throwable $previous = null
    ) {
        $this->result = $result;

        parent::__construct($message, $code, $previous);
    }

    /**
     * Returns policy check result if passed to the exception.
     *
     * @return \Authorization\Policy\ResultInterface|null
     */
    public function getResult(): ?ResultInterface
    {
        return $this->result;
    }
}
