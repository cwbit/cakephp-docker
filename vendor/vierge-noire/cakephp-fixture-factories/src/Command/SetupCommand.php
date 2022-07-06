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
namespace CakephpFixtureFactories\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use CakephpFixtureFactories\Error\FixtureFactoryException;
use CakephpTestSuiteLight\FixtureInjector;
use CakephpTestSuiteLight\FixtureManager;

class SetupCommand extends Command
{
    /**
     * @inheritDoc
     */
    public static function defaultName(): string
    {
        return 'fixture_factories_setup';
    }

    /**
     * @inheritDoc
     */
    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser
            ->setDescription('Helper to setup your phpunit xml file')
            ->addOption('plugin', [
                'help' => 'Set configs in a plugin',
                'short' => 'p',
            ])
            ->addOption('file', [
                'help' => 'Name of the phpunit config file (per default phpunit.xml.dist)',
                'short' => 'f',
            ]);

        return $parser;
    }

    /**
     * @inheritDoc
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $filePath = $this->getPhpunitFilePath($args, $io);
        $this->replaceListenersInPhpunitXmlFile($filePath, $io);
        $io->success("The listener was successfully replaced in $filePath.");
    }

    /**
     * @param \Cake\Console\Arguments $args Arguments
     * @param \Cake\Console\ConsoleIo $io ConsoleIo
     * @return string
     */
    public function getPhpunitFilePath(Arguments $args, ConsoleIo $io): string
    {
        $fileName = $args->getOption('file') ?? 'phpunit.xml.dist';

        $plugin = $args->getOption('plugin');
        if ($plugin) {
            $path = ROOT . DS . 'plugins' . DS . $plugin . DS . $fileName;
        } else {
            $path = ROOT . DS . $fileName;
        }

        if (!file_exists($path)) {
            $io->abort("The phpunit config file $path could not be found.");

            return '';
        } else {
            return $path;
        }
    }

    /**
     * Replaces the listeners and injectors in $filePath
     *
     * @param string $filePath Path to the phpunit file
     * @param \Cake\Console\ConsoleIo $io ConsoleIo
     * @return void
     */
    public function replaceListenersInPhpunitXmlFile(string $filePath, ConsoleIo $io): void
    {
        try {
            $this->replaceListenerInString(
                $filePath,
                file_get_contents($filePath)
            );
        } catch (\Exception $exception) {
            $io->abort($exception->getMessage());
        }
    }

    /**
     * @param string $filePath Path to the phpunit file
     * @param string $string Content of the file
     * @return void
     */
    protected function replaceListenerInString(string $filePath, string $string): void
    {
        $this->existsInString(\Cake\TestSuite\Fixture\FixtureInjector::class, $string, $filePath);
        $this->existsInString(\Cake\TestSuite\Fixture\FixtureManager::class, $string, $filePath);

        $string = str_replace(\Cake\TestSuite\Fixture\FixtureInjector::class, FixtureInjector::class, $string);
        $string = str_replace(\Cake\TestSuite\Fixture\FixtureManager::class, FixtureManager::class, $string);

        file_put_contents($filePath, $string);
    }

    /**
     * Ensure that a string is well found in a file
     *
     * @param string $search Needle
     * @param string $subject Stack
     * @param string $filePath Path to the file
     * @return void
     */
    protected function existsInString(string $search, string $subject, string $filePath): void
    {
        if (strpos($subject, $search) === false) {
            throw new FixtureFactoryException("$search could not be found in $filePath.");
        }
    }
}
