<?php
declare(strict_types=1);

/**
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) 2020 Juan Pablo Ramirez and Nicolas Masson
 * @link          https://webrider.de/
 * @since         2.3.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace CakephpFixtureFactories\Factory;

use Cake\Core\Configure;
use Cake\Utility\Inflector;
use CakephpFixtureFactories\Error\FactoryNotFoundException;

trait FactoryAwareTrait
{
    /**
     * Returns a factory instance from factory or model name
     *
     * Additionnal arguments are passed *as is* to `BaseFactory::make`
     *
     * @param  string           $name          Factory or model name
     * @param  string|array[]   ...$arguments  Additional arguments for `BaseFactory::make`
     * @return \CakephpFixtureFactories\Factory\BaseFactory
     * @throws \CakephpFixtureFactories\Error\FactoryNotFoundException if the factory could not be found
     * @see \CakephpFixtureFactories\Factory\BaseFactory::make
     */
    public function getFactory(string $name, ...$arguments): BaseFactory
    {
        $factoryClassName = $this->getFactoryClassName($name);

        if (class_exists($factoryClassName)) {
            return $factoryClassName::make(...$arguments);
        }

        throw new FactoryNotFoundException("Unable to locate factory class $factoryClassName");
    }

    /**
     * Converts factory or model name to a fully qualified factory class name
     *
     * @param  string $name Factory or model name
     * @return string       Fully qualified class name
     */
    public function getFactoryClassName(string $name): string
    {
        // phpcs:disable
        @[$modelName, $plugin] = array_reverse(explode('.', $name));
        // phpcs:enable

        return $this->getFactoryNamespace($plugin) . '\\' . $this->getFactoryNameFromModelName($modelName);
    }

    /**
     * Returns the factory file name
     *
     * @param  string $name Name of the model or table
     * @return string       [description]
     */
    public function getFactoryFileName(string $name): string
    {
        return str_replace('\\', DIRECTORY_SEPARATOR, $this->getFactoryNameFromModelName($name)) . '.php';
    }

    /**
     * Return the name of the factory from a model name
     *
     * @param string $modelName Name of the model or table
     * @return string
     */
    public static function getFactoryNameFromModelName(string $modelName): string
    {
        return str_replace('/', '\\', Inflector::classify($modelName)) . 'Factory';
    }

    /**
     * Namespace where the factory belongs
     *
     * @param string|null $plugin name of the plugin, or null if no plugin
     * @return string
     */
    public function getFactoryNamespace(?string $plugin = null): string
    {
        if (Configure::check('TestFixtureNamespace')) {
            return Configure::read('TestFixtureNamespace');
        } else {
            return (
                $plugin ?
                    str_replace('/', '\\', $plugin) :
                    Configure::read('App.namespace', 'App')
                ) . '\Test\Factory';
        }
    }
}
