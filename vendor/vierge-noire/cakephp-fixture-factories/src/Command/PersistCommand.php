<?php
declare(strict_types=1);

/**
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) 2020 Juan Pablo Ramirez and Nicolas Masson
 * @link          https://webrider.de/
 * @since         2.3
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace CakephpFixtureFactories\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Datasource\ConnectionManager;
use Cake\Datasource\EntityInterface;
use CakephpFixtureFactories\Error\FactoryNotFoundException;
use CakephpFixtureFactories\Error\PersistenceException;
use CakephpFixtureFactories\Factory\BaseFactory;
use CakephpFixtureFactories\Factory\FactoryAwareTrait;

class PersistCommand extends Command
{
    use FactoryAwareTrait;

    public const ARG_NAME = 'model';

    /**
     * @inheritDoc
     */
    public static function defaultName(): string
    {
        return 'fixture_factories_persist';
    }

    /**
     * @inheritDoc
     */
    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser
            ->setDescription('Helper to persist test fixtures on the command line')
            ->addArgument(self::ARG_NAME, [
                'help' => 'The model to persist, accepts plugin notation. Or provide a fully qualified factory class.',
                'required' => true,
            ])
            ->addOption('plugin', [
                'help' => 'Fetch the factory in a plugin.',
                'short' => 'p',
            ])
            ->addOption('connection', [
                'help' => 'Persist into this connection.',
                'short' => 'c',
                'default' => 'test',
            ])
            ->addOption('method', [
                'help' => 'Call this method defined in the factory class concerned.',
                'short' => 'm',
            ])
            ->addOption('number', [
                'help' => 'Number of entities to persist.',
                'short' => 'n',
                'default' => 1,
            ])
            ->addOption('with', [
                'help' => 'With associated entity/entities.',
                'short' => 'w',
            ])
            ->addOption('dry-run', [
                'help' => 'Display the entities created without persisting.',
                'short' => 'd',
                'boolean' => true,
            ]);

        return $parser;
    }

    /**
     * @inheritDoc
     */
    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $factory = null;
        try {
            $factory = $this->parseFactory($args);
            // The following order is important, as methods may overwrite $times
            $this->setTimes($args, $factory);
            $this->with($args, $factory);
            $this->attachMethod($args, $factory, $io);
        } catch (FactoryNotFoundException $e) {
            $io->error($e->getMessage());
            $this->abort();
        }
        if ($args->getOption('dry-run')) {
            $this->dryRun($factory, $io);
        } else {
            $this->persist($factory, $args, $io);
        }

        return self::CODE_SUCCESS;
    }

    /**
     * @param \Cake\Console\Arguments $args The command arguments
     * @return \CakephpFixtureFactories\Factory\BaseFactory
     * @throws \CakephpFixtureFactories\Error\FactoryNotFoundException if the factory could not be found
     */
    public function parseFactory(Arguments $args): BaseFactory
    {
        $factoryString = $args->getArgument(self::ARG_NAME);

        if (is_subclass_of($factoryString, BaseFactory::class)) {
            return $factoryString::make();
        }

        $plugin = $args->getOption('plugin');
        if (is_string($plugin)) {
            $factoryString = $plugin . '.' . $factoryString;
        }

        return $this->getFactory($factoryString);
    }

    /**
     * @param \Cake\Console\Arguments $args Arguments
     * @param \CakephpFixtureFactories\Factory\BaseFactory $factory Factory
     * @return \CakephpFixtureFactories\Factory\BaseFactory
     */
    public function setTimes(Arguments $args, BaseFactory $factory): BaseFactory
    {
        if (!empty($args->getOption('number'))) {
            $times = (int)$args->getOption('number');
        } else {
            $times = 1;
        }

        return $factory->setTimes($times);
    }

    /**
     * @param \Cake\Console\Arguments $args Arguments
     * @param \CakephpFixtureFactories\Factory\BaseFactory $factory Factory
     * @param \Cake\Console\ConsoleIo $io Console
     * @return \CakephpFixtureFactories\Factory\BaseFactory
     * @throws \CakephpFixtureFactories\Error\FactoryNotFoundException if the method is not found in the factory
     */
    public function attachMethod(Arguments $args, BaseFactory $factory, ConsoleIo $io): BaseFactory
    {
        $method = $args->getOption('method');

        if ($method === null) {
            return $factory;
        }
        if (!method_exists($factory, $method)) {
            $className = get_class($factory);
            $io->error("The method {$method} was not found in {$className}.");
            throw new FactoryNotFoundException();
        }

        return $factory->{$method}();
    }

    /**
     * @param \Cake\Console\Arguments $args Arguments
     * @param \CakephpFixtureFactories\Factory\BaseFactory $factory Factory
     * @return \CakephpFixtureFactories\Factory\BaseFactory
     */
    public function with(Arguments $args, BaseFactory $factory)
    {
        $with = $args->getOption('with');

        if ($with === null) {
            return $factory;
        }

        return $factory->with($with);
    }

    /**
     * Sets the connection passed in argument as the target connection,
     * overwriting the table's default connection.
     *
     * @param string $connection Connection name
     * @param \CakephpFixtureFactories\Factory\BaseFactory $factory Factory
     * @return void
     */
    public function aliasConnection(string $connection, BaseFactory $factory): void
    {
        ConnectionManager::alias(
            $connection,
            $factory->getTable()->getConnection()->configName()
        );
    }

    /**
     * @param \CakephpFixtureFactories\Factory\BaseFactory $factory Factory
     * @param \Cake\Console\Arguments $args Arguments
     * @param \Cake\Console\ConsoleIo $io Console
     * @return void
     */
    public function persist(BaseFactory $factory, Arguments $args, ConsoleIo $io): void
    {
        $connection = $args->getOption('connection') ?? 'test';
        $this->aliasConnection($connection, $factory);

        $entities = [];
        try {
            $entities = $factory->persist();
        } catch (PersistenceException $e) {
            $io->error($e->getMessage());
            $this->abort();
        }

        $times = is_subclass_of($entities, EntityInterface::class) ? 1 : count($entities);
        $factory = get_class($factory);
        $io->success("{$times} {$factory} persisted on '{$connection}' connection.");
    }

    /**
     * @param \CakephpFixtureFactories\Factory\BaseFactory $factory Factory
     * @param \Cake\Console\ConsoleIo $io Console
     * @return void
     */
    public function dryRun(BaseFactory $factory, ConsoleIo $io): void
    {
        $entities = $factory->getEntities();
        $times = count($entities);
        $factory = get_class($factory);

        $io->success("{$times} {$factory} generated on dry run.");
        $eol = PHP_EOL;
        foreach ($entities as $i => $entity) {
            $io->hr();
            $io->info("[$i]");
            $output = json_encode($entity->toArray(), JSON_PRETTY_PRINT);
            $io->info($output);
        }
    }
}
