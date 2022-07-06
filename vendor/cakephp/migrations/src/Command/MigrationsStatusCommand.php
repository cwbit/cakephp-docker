<?php
declare(strict_types=1);

/**
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Migrations\Command;

/**
 * This class is needed in order to provide a correct autocompletion feature
 * when using the CakePHP migrations plugin. It has no effect on the
 * migrations process.
 */
class MigrationsStatusCommand extends MigrationsCommand
{
    /**
     * Phinx command name.
     *
     * @var string
     */
    protected static $commandName = 'Status';
}
