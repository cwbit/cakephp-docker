<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         2.0.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Bake\Command;

use Bake\Shell\Task\BakeTask;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\Command\HelpCommand;
use Cake\Console\CommandCollection;
use Cake\Console\CommandCollectionAwareInterface;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Console\Exception\ConsoleException;
use Cake\Console\Shell;
use Cake\Core\Configure;
use Cake\Core\Plugin as CorePlugin;
use Cake\Utility\Inflector;

/**
 * Command that provides help and an entry point to bake tools.
 */
class EntryCommand extends Command implements CommandCollectionAwareInterface
{
    /**
     * The command collection to get help on.
     *
     * @var \Cake\Console\CommandCollection
     */
    protected $commands;

    /**
     * The HelpCommand to get help.
     *
     * @var \Cake\Console\Command\HelpCommand
     */
    protected $help;

    /**
     * @inheritDoc
     */
    public static function defaultName(): string
    {
        return 'bake';
    }

    /**
     * @inheritDoc
     */
    public function setCommandCollection(CommandCollection $commands): void
    {
        $this->commands = $commands;
    }

    /**
     * Run the command.
     *
     * Override the run() method so that we can splice in dynamic
     * subcommand handling for legacy tasks.
     *
     * @param array $argv Arguments from the CLI environment.
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return int|null Exit code or null for success.
     */
    public function run(array $argv, ConsoleIo $io): ?int
    {
        $this->initialize();

        $parser = $this->getOptionParser();
        try {
            [$options, $arguments] = $parser->parse($argv);
            $args = new Arguments(
                $arguments,
                $options,
                $parser->argumentNames()
            );
        } catch (ConsoleException $e) {
            $io->err('Error: ' . $e->getMessage());

            return static::CODE_ERROR;
        }
        $this->setOutputLevel($args, $io);

        // This is the variance from Command::run()
        if (!$args->getArgumentAt(0) && $args->getOption('help')) {
            $this->executeCommand($this->help, [], $io);

            return static::CODE_SUCCESS;
        }

        return $this->execute($args, $io);
    }

    /**
     * Execute the command.
     *
     * This command acts as a catch-all for legacy tasks that may
     * be defined in the application or plugins.
     *
     * @param \Cake\Console\Arguments $args The command arguments.
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return int|null The exit code or null for success
     */
    public function execute(Arguments $args, ConsoleIo $io): ?int
    {
        if ($args->hasArgumentAt(0)) {
            $name = $args->getArgumentAt(0);
            $task = $this->createTask($name, $io);
            if ($task) {
                $argList = $args->getArguments();

                // Remove command name.
                array_shift($argList);
                foreach ($args->getOptions() as $key => $value) {
                    if ($value === false) {
                        continue;
                    } elseif ($value === true) {
                        $argList[] = '--' . $key;
                    } else {
                        $argList[] = '--' . $key;
                        $argList[] = $value;
                    }
                }

                $result = $task->runCommand($argList);
                if ($result === false) {
                    return static::CODE_ERROR;
                }
                if ($result === true) {
                    return static::CODE_SUCCESS;
                }

                return $result;
            }
            $io->err("<error>Could not find a task named `{$name}`.</error>");

            return static::CODE_ERROR;
        }
        $io->err('<warning>No command provided. Run `bake --help` to get a list of commands.</warning>');

        return static::CODE_ERROR;
    }

    /**
     * Find and create a Shell based BakeTask
     *
     * @param string $name The task name.
     * @param \Cake\Console\ConsoleIo $io The console io.
     * @return \Cake\Console\Shell|null
     */
    protected function createTask(string $name, ConsoleIo $io): ?Shell
    {
        $found = false;
        $name = Inflector::camelize($name);
        $factory = function ($className, $io) {
            $task = new $className($io);
            $task->setRootName('cake bake');

            return $task;
        };

        // Look in each plugin for the requested task
        foreach (CorePlugin::loaded() as $plugin) {
            $namespace = str_replace('/', '\\', $plugin);
            $candidate = $namespace . '\Shell\Task\\' . $name . 'Task';
            if (class_exists($candidate) && is_subclass_of($candidate, BakeTask::class)) {
                return $factory($candidate, $io);
            }
        }

        // Try the app as well
        $namespace = Configure::read('App.namespace');
        $candidate = $namespace . '\Shell\Task\\' . $name . 'Task';
        if (class_exists($candidate) && is_subclass_of($candidate, BakeTask::class)) {
            return $factory($candidate, $io);
        }

        return null;
    }

    /**
     * Gets the option parser instance and configures it.
     *
     * @param \Cake\Console\ConsoleOptionParser $parser The console option parser
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $this->help = new HelpCommand();
        /** @psalm-suppress InaccessibleMethod Protected methods as class based */
        $parser = $this->help->buildOptionParser($parser);
        $parser
            ->setDescription(
                'Bake generates code for your application. Different types of classes can be generated' .
                ' with the subcommands listed below. For example run <info>bake controller --help</info>' .
                ' to learn more about generating a controller.'
            )
            ->setEpilog('Older Shell based tasks will not be listed here, but can still be run.');
        $commands = [];
        foreach ($this->commands as $command => $class) {
            if (substr($command, 0, 4) === 'bake') {
                $parts = explode(' ', $command);

                // Remove `bake`
                array_shift($parts);
                if (count($parts) === 0) {
                    continue;
                }
                $commands[$command] = $class;
            }
        }

        $CommandCollection = new CommandCollection($commands);
        $this->help->setCommandCollection($CommandCollection);

        return $parser;
    }
}
