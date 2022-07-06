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

namespace CakeDC\Auth\Social\Service;

use Cake\Core\InstanceConfigTrait;

abstract class OAuthServiceAbstract implements ServiceInterface
{
    use InstanceConfigTrait;

    /**
     * The default config.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    /**
     * The provider name.
     *
     * @var string
     */
    protected $providerName = '';

    /**
     * Get the social provider name
     *
     * @return string
     */
    public function getProviderName(): string
    {
        return $this->providerName;
    }

    /**
     * Set the social provider name
     *
     * @param string $providerName social provider
     * @return self
     */
    public function setProviderName(string $providerName)
    {
        $this->providerName = $providerName;

        return $this;
    }
}
