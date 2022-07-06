<?php
declare(strict_types=1);

/**
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) 2020 Juan Pablo Ramirez and Nicolas Masson
 * @link          https://webrider.de/
 * @since         1.0.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace CakephpFixtureFactories\Factory;

use Cake\Core\Configure;
use Cake\ORM\Table;
use CakephpFixtureFactories\ORM\FactoryTableRegistry;

/**
 * Class EventCollector
 *
 * @internal
 */
class EventCollector
{
    public const MODEL_EVENTS = 'CakephpFixtureFactoriesListeningModelEvents';
    public const MODEL_BEHAVIORS = 'CakephpFixtureFactoriesListeningBehaviors';

    /**
     * @var \Cake\ORM\Table|null
     */
    private $table;

    /**
     * @var array
     */
    private $listeningBehaviors = [];

    /**
     * @var array
     */
    private $listeningModelEvents = [];

    /**
     * @var array
     */
    private $defaultListeningBehaviors = [];

    /**
     * @var string
     */
    private $rootTableRegistryName;

    /**
     * EventCollector constructor.
     *
     * @param string $rootTableRegistryName Name of the model of the master factory
     */
    public function __construct(string $rootTableRegistryName)
    {
        $this->rootTableRegistryName = $rootTableRegistryName;
        $this->setDefaultListeningBehaviors();
    }

    /**
     * Create a table cloned from the TableRegistry
     * and per default without Model Events.
     *
     * @return \Cake\ORM\Table
     */
    public function getTable(): Table
    {
        if (isset($this->table)) {
            return $this->table;
        }

        $options = [
            self::MODEL_EVENTS => $this->getListeningModelEvents(),
            self::MODEL_BEHAVIORS => $this->getListeningBehaviors(),
        ];

        try {
            $table = FactoryTableRegistry::getTableLocator()->get($this->rootTableRegistryName, $options);
        } catch (\RuntimeException $exception) {
            FactoryTableRegistry::getTableLocator()->remove($this->rootTableRegistryName);
            $table = FactoryTableRegistry::getTableLocator()->get($this->rootTableRegistryName, $options);
        }

        return $this->table = $table;
    }

    /**
     * @return array
     */
    public function getListeningBehaviors(): array
    {
        return $this->listeningBehaviors;
    }

    /**
     * @param array $activeBehaviors Behaviors the factory will listen to
     * @return array
     */
    public function listeningToBehaviors(array $activeBehaviors): array
    {
        unset($this->table);

        return $this->listeningBehaviors = array_merge($this->defaultListeningBehaviors, $activeBehaviors);
    }

    /**
     * @param array $activeModelEvents Events the factory will listen to
     * @return array
     */
    public function listeningToModelEvents(array $activeModelEvents): array
    {
        unset($this->table);

        return $this->listeningModelEvents = $activeModelEvents;
    }

    /**
     * @return array
     */
    public function getListeningModelEvents(): array
    {
        return $this->listeningModelEvents;
    }

    /**
     * @return void
     */
    protected function setDefaultListeningBehaviors(): void
    {
        $defaultBehaviors = (array)Configure::read('TestFixtureGlobalBehaviors', []);
        $defaultBehaviors[] = 'Timestamp';
        $this->defaultListeningBehaviors = $defaultBehaviors;
        $this->listeningBehaviors = $defaultBehaviors;
    }

    /**
     * @return array
     */
    public function getDefaultListeningBehaviors(): array
    {
        return $this->defaultListeningBehaviors;
    }
}
