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
namespace CakeDC\Auth\Test;

use Cake\Http\MiddlewareQueue;

/**
 * Class TestApplication
 *
 * @package CakeDC\Auth\Test
 */
class TestApplication extends \Cake\Http\BaseApplication
{
    /**
     * Setup the middleware queue
     *
     * @param \Cake\Http\MiddlewareQueue $middleware The middleware queue to set in your App Class
     * @return \Cake\Http\MiddlewareQueue
     */
    public function middleware(MiddlewareQueue $middleware): MiddlewareQueue
    {
        return $middleware;
    }

    /**
     * @inheritDoc
     */
    public function bootstrap(): void
    {
        parent::bootstrap();
        $this->addPlugin('CakeDC/Auth', [
            'path' => dirname(__FILE__, 2) . DS,
            'routes' => true,
            'bootstrap' => true,
        ]);
    }
}
