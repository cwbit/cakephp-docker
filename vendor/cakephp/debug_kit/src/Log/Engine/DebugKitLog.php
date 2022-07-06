<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugKit\Log\Engine;

use Cake\Log\Engine\BaseLog;

/**
 * A CakeLog listener which saves having to munge files or other configured loggers.
 */
class DebugKitLog extends BaseLog
{
    /**
     * logs
     *
     * @var array
     */
    protected $_logs = [];

    /**
     * Captures log messages in memory
     *
     * @param mixed $level The type of message being logged.
     * @param string $message The message being logged.
     * @param array $context Additional context data
     * @return void
     */
    public function log($level, $message, array $context = [])
    {
        if (!isset($this->_logs[$level])) {
            $this->_logs[$level] = [];
        }
        $this->_logs[$level][] = [date('Y-m-d H:i:s'), $this->interpolate($message)];
    }

    /**
     * Get the logs.
     *
     * @return array
     */
    public function all()
    {
        return $this->_logs;
    }

    /**
     * Get the number of log entries.
     *
     * @return int
     */
    public function count()
    {
        return array_reduce($this->_logs, function ($sum, $v) {
            return $sum + count($v);
        }, 0);
    }

    /**
     * Check if there are no logs.
     *
     * @return bool
     */
    public function noLogs()
    {
        return empty($this->_logs);
    }
}
