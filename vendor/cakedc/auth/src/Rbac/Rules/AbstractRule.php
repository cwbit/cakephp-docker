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
namespace CakeDC\Auth\Rbac\Rules;

use Cake\Core\InstanceConfigTrait;
use Cake\Datasource\ModelAwareTrait;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use OutOfBoundsException;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class AbstractRule
 *
 * @method \Cake\ORM\Table loadModel($modelClass = null, $modelType = null)
 * @package CakeDC\Auth\Auth\Rules
 */
abstract class AbstractRule implements Rule
{
    use InstanceConfigTrait;
    use LocatorAwareTrait;
    use ModelAwareTrait;

    /**
     * @var array default config
     */
    protected $_defaultConfig = [];

    /**
     * AbstractRule constructor.
     *
     * @param array $config Rule config
     */
    public function __construct($config = [])
    {
        $this->setConfig($config);
    }

    /**
     * Get a table from the alias, table object or inspecting the request for a default table
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request request
     * @param mixed $table table
     * @return \Cake\ORM\Table
     */
    protected function _getTable(ServerRequestInterface $request, $table = null)
    {
        if (empty($table)) {
            return $this->_getTableFromRequest($request);
        }
        if ($table instanceof Table) {
            return $table;
        }

        return TableRegistry::getTableLocator()->get($table);
    }

    /**
     * Inspect the request and try to retrieve a table based on the current controller
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request request
     * @return \Cake\ORM\Table
     * @throws \OutOfBoundsException if table alias can't be extracted from request
     */
    protected function _getTableFromRequest(ServerRequestInterface $request)
    {
        $params = $request->getAttribute('params');

        $plugin = $params['plugin'] ?? null;
        $controller = $params['controller'] ?? null;
        $modelClass = ($plugin ? $plugin . '.' : '') . $controller;

        $this->modelFactory('Table', function (string $alias, array $options): \Cake\ORM\Table {
            return $this->getTableLocator()->get($alias, $options);
        });
        if (empty($modelClass)) {
            throw new OutOfBoundsException('Missing Table alias, we could not extract a default table from the request');
        }

        return $this->loadModel($modelClass);
    }

    /**
     * Check the current entity is owned by the logged in user
     *
     * @param array|\ArrayAccess $user Auth array with the logged in data
     * @param string $role role of the user
     * @param \Psr\Http\Message\ServerRequestInterface $request current request, used to get a default table if not provided
     * @return bool
     * @throws \OutOfBoundsException if table is not found or it doesn't have the expected fields
     */
    abstract public function allowed($user, $role, ServerRequestInterface $request);
}
