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

use Authorization\Exception\Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * This interface defines an API for handling unauthorized requests.
 */
interface HandlerInterface
{
    /**
     * Handles the unauthorized request. The modified response should be returned.
     *
     * @param \Authorization\Exception\Exception $exception Authorization exception thrown by the application.
     * @param \Psr\Http\Message\ServerRequestInterface $request Server request.
     * @param array $options Options array.
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function handle(
        Exception $exception,
        ServerRequestInterface $request,
        array $options = []
    ): ResponseInterface;
}
