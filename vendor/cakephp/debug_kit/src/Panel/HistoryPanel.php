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

use Cake\ORM\Locator\LocatorAwareTrait;
use DebugKit\DebugPanel;

/**
 * Provides debug information on previous requests.
 */
class HistoryPanel extends DebugPanel
{
    use LocatorAwareTrait;

    /**
     * Get the data for the panel.
     *
     * @return array
     */
    public function data()
    {
        $table = $this->getTableLocator()->get('DebugKit.Requests');
        $recent = $table->find('recent');

        return [
            'requests' => $recent->toArray(),
        ];
    }

    /**
     * Gets the initial text for the history summary
     *
     * @return string
     */
    public function summary()
    {
        return '0 xhr';
    }
}
