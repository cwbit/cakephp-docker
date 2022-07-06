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
 * @since         1.0.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugKit\View\Helper;

use Cake\View\Helper;

/**
 * Class SimpleGraphHelper
 *
 * Allows creation and display of extremely simple graphing elements
 *
 * @since         DebugKit 1.0
 */
class SimpleGraphHelper extends Helper
{
    /**
     * Default settings to be applied to each Simple Graph
     *
     * Allowed options:
     *
     * - max => (int) Maximum value in the graphs
     * - width => (int)
     * - valueType => string (value, percentage)
     * - style => array
     *
     * @var array
     */
    protected $_defaultSettings = [
        'max' => 100,
        'width' => 350,
        'valueType' => 'value',
    ];

    /**
     * bar method
     *
     * @param float $value Value to be graphed
     * @param int $offset how much indentation
     * @param array $options Graph options
     * @return string Html graph
     */
    public function bar($value, $offset, $options = [])
    {
        $settings = array_merge($this->_defaultSettings, $options);
        $max = $settings['max'];
        $width = $settings['width'];
        $valueType = $settings['valueType'];

        $graphValue = $value / $max * $width;
        $graphValue = max(round($graphValue), 1);

        if ($valueType === 'percentage') {
            $graphOffset = 0;
        } else {
            $graphOffset = $offset / $max * $width;
            $graphOffset = round($graphOffset);
        }

        return sprintf(
            '<div class="graph-bar" style="%s"><div class="graph-bar-value" style="%s" title="%s"> </div></div>',
            "width: {$width}px",
            "margin-left: {$graphOffset}px; width: {$graphValue}px",
            __d('debug_kit', 'Starting {0}ms into the request, taking {1}ms', $offset, $value)
        );
    }
}
