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
namespace DebugKit\Panel;

use Cake\I18n\Number;
use DebugKit\DebugMemory;
use DebugKit\DebugPanel;
use DebugKit\DebugTimer;

/**
 * Provides debug information on all timers used in a request.
 */
class TimerPanel extends DebugPanel
{
    /**
     * Return an array of events to listen to.
     *
     * @return array<string, mixed>
     */
    public function implementedEvents(): array
    {
        $before = function ($name) {
            return function () use ($name) {
                DebugTimer::start($name);
            };
        };
        $after = function ($name) {
            return function () use ($name) {
                DebugTimer::stop($name);
            };
        };
        $both = function ($name) use ($before, $after) {
            return [
                ['priority' => 0, 'callable' => $before('Event: ' . $name)],
                ['priority' => 999, 'callable' => $after('Event: ' . $name)],
            ];
        };

        return [
            'Controller.initialize' => [
                ['priority' => 0, 'callable' => function () {
                    DebugMemory::record('Controller initialization');
                }],
                ['priority' => 0, 'callable' => $before('Event: Controller.initialize')],
                ['priority' => 999, 'callable' => $after('Event: Controller.initialize')],
            ],
            'Controller.startup' => [
                ['priority' => 0, 'callable' => $before('Event: Controller.startup')],
                ['priority' => 999, 'callable' => $after('Event: Controller.startup')],
                ['priority' => 999, 'callable' => function () {
                    DebugMemory::record('Controller action start');
                    DebugTimer::start('Controller: action');
                }],
            ],
            'Controller.beforeRender' => [
                ['priority' => 0, 'callable' => function () {
                    DebugTimer::stop('Controller: action');
                }],
                ['priority' => 0, 'callable' => $before('Event: Controller.beforeRender')],
                ['priority' => 999, 'callable' => $after('Event: Controller.beforeRender')],
                ['priority' => 999, 'callable' => function () {
                    DebugMemory::record('View Render start');
                    DebugTimer::start('View: Render');
                }],
            ],
            'View.beforeRender' => $both('View.beforeRender'),
            'View.afterRender' => $both('View.afterRender'),
            'View.beforeLayout' => $both('View.beforeLayout'),
            'View.afterLayout' => $both('View.afterLayout'),
            'View.beforeRenderFile' => [
                ['priority' => 0, 'callable' => function ($event, $filename) {
                    DebugTimer::start('Render File: ' . $filename);
                }],
            ],
            'View.afterRenderFile' => [
                ['priority' => 0, 'callable' => function ($event, $filename) {
                    DebugTimer::stop('Render File: ' . $filename);
                }],
            ],
            'Controller.shutdown' => [
                ['priority' => 0, 'callable' => $before('Event: Controller.shutdown')],
                ['priority' => 0, 'callable' => function () {
                    DebugTimer::stop('View: Render');
                    DebugMemory::record('Controller shutdown');
                }],
                ['priority' => 999, 'callable' => $after('Event: Controller.shutdown')],
            ],
        ];
    }

    /**
     * Get the data for the panel.
     *
     * @return array
     */
    public function data()
    {
        return [
            'requestTime' => DebugTimer::requestTime(),
            'timers' => DebugTimer::getAll(),
            'memory' => DebugMemory::getAll(),
            'peakMemory' => DebugMemory::getPeak(),
        ];
    }

    /**
     * Get the summary for the panel.
     *
     * @return string
     */
    public function summary()
    {
        $time = Number::precision(DebugTimer::requestTime(), 2) . ' s';
        $memory = Number::toReadableSize(DebugMemory::getPeak());

        return "$time / $memory";
    }
}
