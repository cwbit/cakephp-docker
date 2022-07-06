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
namespace Authorization\Policy;

/**
 * Policy resolver interface.
 */
interface ResolverInterface
{
    /**
     * Resolves the policy object based on the authorization resource.
     *
     * The resolver MUST throw the `\Authorization\Policy\Exception\MissingPolicyException`
     * exception if a policy cannot be resolved for a given resource.
     *
     * @param mixed $resource A resource that the access is checked against.
     * @return mixed
     * @throws \Authorization\Policy\Exception\MissingPolicyException If a policy cannot be resolved.
     */
    public function getPolicy($resource);
}
