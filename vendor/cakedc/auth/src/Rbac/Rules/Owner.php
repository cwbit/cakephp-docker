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

use Cake\Utility\Hash;
use OutOfBoundsException;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Owner rule class, used to match ownership permissions
 */
class Owner extends AbstractRule
{
    public const TYPE_TABLE_KEY_PARAMS = 'params';
    public const TYPE_TABLE_KEY_QUERY = 'query';
    public const TYPE_TABLE_KEY_DATA = 'data';

    protected $_defaultConfig = [
        //field in the owned table matching the user_id
        'ownerForeignKey' => 'user_id',
        /*
         * request key type to retrieve the table id, could be "params", "query", "data" to locate the table id
         * example:
         *   example.com/controller/action/XXX would be
         *     tableKeyType => 'params', 'tableIdParamsKey' => 'pass.0'
         *   example.com/controller/action?post_id=XXX would be
         *     tableKeyType => 'query', 'tableIdParamsKey' => 'post_id'
         *   example.com/controller/action [posted form with a field named post_id] would be
         *     tableKeyType => 'data', 'tableIdParamsKey' => 'post_id'
         */
        'tableKeyType' => self::TYPE_TABLE_KEY_PARAMS,
        // key path to retrieve the owned table id from the specified params, query or data depending on the 'tableKeyType'
        'tableIdParamsKey' => 'pass.0',
        /*
         * define table to use or pick it from controller name defaults if null
         * if null, table used will be based on current controller's default table
         * if string, TableRegistry::get will be used
         * if Table, the table object will be used
         */
        'table' => null,
        /*
         * define the table id to be used to match the row id, this is useful when checking belongsToMany associations
         * Example: If checking ownership in a PostsUsers table, we should use 'id' => 'post_id'
         * If value is null, we'll use the $table->primaryKey()
         */
        'id' => null,
        'conditions' => [],
    ];

    /**
     * @inheritDoc
     */
    public function allowed($user, $role, ServerRequestInterface $request)
    {
        $table = $this->_getTable($request, $this->getConfig('table'));
        //retrieve entity id from request
        $id = $this->getTableId($request);
        $userId = $user['id'] ?? null;
        if ($userId === null) {
            return false;
        }

        try {
            if (!$table->hasField($this->getConfig('ownerForeignKey'))) {
                $msg = sprintf(
                    'Missing column %s in table %s while checking ownership permissions for user %s',
                    $this->getConfig('ownerForeignKey'),
                    $table->getAlias(),
                    $userId
                );
                throw new OutOfBoundsException($msg);
            }
        } catch (\Cake\Core\Exception\CakeException $ex) {
            $msg = sprintf(
                'Missing column %s in table %s while checking ownership permissions for user %s',
                $this->getConfig('ownerForeignKey'),
                $table->getAlias(),
                $userId
            );
            throw new OutOfBoundsException($msg, $ex->getCode(), $ex);
        }
        $idColumn = $this->getConfig('id');
        if (empty($idColumn)) {
            $idColumn = $table->getPrimaryKey();
        }
        $conditions = array_merge([
            $idColumn => $id,
            $this->getConfig('ownerForeignKey') => $userId,
        ], $this->getConfig('conditions'));

        return $table->exists($conditions);
    }

    /**
     * Get the table id, inspecting the request
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request request
     * @return string
     * @throws \RuntimeException when invalid table key is used
     */
    protected function getTableId(ServerRequestInterface $request)
    {
        $tableKeyType = $this->getConfig('tableKeyType');
        switch ($tableKeyType) {
            case self::TYPE_TABLE_KEY_PARAMS:
                $requestKeyTypeData = $request->getAttribute('params') ?: [];
                break;
            case self::TYPE_TABLE_KEY_QUERY:
                $requestKeyTypeData = $request->getQueryParams() ?: [];
                break;
            case self::TYPE_TABLE_KEY_DATA:
                $requestKeyTypeData = $request->getParsedBody() ?: [];
                break;
            default:
                throw new \RuntimeException(sprintf('TypeTableKey "%s" is invalid, please use "params", "data" or "query"', $tableKeyType));
        }

        return Hash::get($requestKeyTypeData, $this->getConfig('tableIdParamsKey'));
    }
}
