<?php
declare(strict_types=1);

namespace AssetMix\Command;

use AssetMix\StubsPathTrait;
use AssetMix\Utility\FileUtility;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Exception;

class AssetMixCommand extends Command
{
    use StubsPathTrait;

    /**
     * Filesystem utility object
     *
     * @var \AssetMix\Utility\FileUtility
     */
    private $filesystem;

    /**
     * Preset type provided via argument.
     *
     * @var string|null
     */
    private $preset;

    /**
     * Directory name where all assets(js, css) files will reside.
     */
    public const ASSETS_DIR_NAME = 'assets';

    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        $this->filesystem = new FileUtility();
    }

    /**
     * @inheritDoc
     */
    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser
            ->setDescription('Generate configuration files, and assets directory skeleton')
            ->addArgument('preset', [
                'help' => __('The preset/scaffolding type. Defaults to <info>vue</info>'),
                'choices' => ['bootstrap', 'vue', 'react', 'inertia-vue', 'inertia-react'],
                'default' => 'vue',
            ])
            ->addOption('dir', [
                'short' => 'd',
                'help' => __('Directory name to create'),
                'default' => self::ASSETS_DIR_NAME,
            ]);

        return $parser;
    }

    /**
     * @inheritDoc
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $this->preset = $args->getArgument('preset');

        if ($this->preset === null) {
            $this->preset = 'vue';
        }

        $this->updatePackageJsonFile($io);
        $this->copyWebpackMixJsFile($args, $io);
        $this->copyAssetsDirectory($args, $io);

        $io->info('Note: You should run "npm install && npm run dev" to compile your updated scaffolding.');

        return null;
    }

    /**
     * Update `package.json` file from stubs directory and write into project root.
     *
     * @param \Cake\Console\ConsoleIo $io Console input/output
     * @return void
     */
    private function updatePackageJsonFile($io)
    {
        $path = $this->getPackageJsonPath();

        $packages = $this->getPackageJsonFileContentsAsArray();

        $this->writePackageJsonFile($packages, $path['to']);

        $io->success(__('\'package.json\' file created successfully.'));
    }

    /**
     * Writes `package.json` file.
     *
     * @param  array<mixed> $packages Content to write into the file.
     * @param  string $to Path to create the file.
     * @return void
     */
    private function writePackageJsonFile($packages, $to)
    {
        if (! is_string($this->preset)) {
            throw new Exception('Invalid preset value');
        }

        $packageConfigKey = 'devDependencies';
        $updatePackagesMethodName = sprintf(
            'update%sPackagesArray',
            ucwords(str_replace('-', '', $this->preset))
        );

        $packages[$packageConfigKey] = $this->{$updatePackagesMethodName}($packages[$packageConfigKey]);

        ksort($packages[$packageConfigKey]);

        file_put_contents(
            $to,
            json_encode($packages, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . PHP_EOL
        );
    }

    /**
     * Copy `webpack.mix.js` file in project root
     *
     * @param \Cake\Console\Arguments $args Arguments
     * @param \Cake\Console\ConsoleIo $io Console input/output
     * @return void
     */
    private function copyWebpackMixJsFile($args, $io)
    {
        $dirName = $args->getOption('dir');

        if (! is_string($dirName)) {
            throw new Exception('Invalid directory name');
        }

        $path = $this->getWebpackMixJsPath();
        $content = $this->setWebpackMixFileContents($path['from'], $dirName);

        $this->filesystem->write($path['to'], $content);

        $io->success(__('\'webpack.mix.js\' file created successfully.'));
    }

    /**
     * Create, copy `assets` directory to project of the root
     *
     * @param \Cake\Console\Arguments $args Arguments
     * @param \Cake\Console\ConsoleIo $io Console input/output
     * @return void
     */
    private function copyAssetsDirectory($args, $io)
    {
        $dirName = $args->getOption('dir');
        $assetPath = ROOT . DS . $dirName;
        $stubsPaths = $this->getAssetsDirPaths();

        if ($this->filesystem->exists($assetPath)) {
            // Ask if they want to overwrite existing directory with default stubs
        }

        $this->filesystem->mkdir($assetPath);
        $this->filesystem->recursiveCopy($stubsPaths['from_assets'], $assetPath);

        $io->success(__(sprintf('\'%s\' directory created successfully.', $dirName)));
    }

    /**
     * Update `webpack.mix.js` file contents with given directory name.
     *
     * @param string $filePath Path to file.
     * @param string $dirName Directory name.
     * @return string Updated file contents.
     */
    private function setWebpackMixFileContents($filePath, $dirName)
    {
        $currentWebpackContents = file_get_contents($filePath);

        if (! is_string($currentWebpackContents)) {
            throw new Exception('Invalid webpack.mix.js file contents');
        }

        $updatedFileContents = preg_replace(
            '/\b' . self::ASSETS_DIR_NAME . '\b/',
            $dirName,
            $currentWebpackContents
        );

        if (! is_string($updatedFileContents)) {
            throw new Exception('Unable to replace file content');
        }

        return $updatedFileContents;
    }

    /**
     * Get `package.json` file path depending on preset.
     *
     * @return array<string>
     */
    private function getPackageJsonPath()
    {
        if (! is_string($this->preset)) {
            throw new Exception('Invalid preset value');
        }

        $getPackgeJsonPathMethodName = sprintf(
            'get%sPackageJsonPath',
            ucwords(str_replace('-', '', $this->preset))
        );

        return $this->{$getPackgeJsonPathMethodName}();
    }

    /**
     * Get `package.json` file contents as array depending on preset.
     *
     * @return array<mixed>
     */
    private function getPackageJsonFileContentsAsArray()
    {
        if (! is_string($this->preset)) {
            throw new Exception('Invalid preset value');
        }

        $getPackgeJsonPathMethodName = sprintf(
            'get%sPackageJsonPath',
            ucwords(str_replace('-', '', $this->preset))
        );
        $path = $this->{$getPackgeJsonPathMethodName}();

        if (! is_string($path['from'])) {
            throw new Exception('Invalid path');
        }

        return json_decode((string)file_get_contents($path['from']), true);
    }

    /**
     * Returns `webpack.mix.js` file path depending on preset.
     *
     * @return array<string>
     */
    private function getWebpackMixJsPath()
    {
        if (! is_string($this->preset)) {
            throw new Exception('Invalid preset value');
        }

        $webpackMixJsPathMethodName = sprintf(
            'get%sWebpackMixJsPath',
            ucwords(str_replace('-', '', $this->preset))
        );

        return $this->{$webpackMixJsPathMethodName}();
    }

    /**
     * Returns paths of `assets` directory files depending on preset.
     *
     * @return array<string>
     */
    private function getAssetsDirPaths()
    {
        if (! is_string($this->preset)) {
            throw new Exception('Invalid preset value');
        }

        $assetsDirPathMethodName = sprintf(
            'get%sAssetsDirPaths',
            ucwords(str_replace('-', '', $this->preset))
        );

        return $this->{$assetsDirPathMethodName}();
    }

    /**
     * Update packages array for vue.
     *
     * @phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements
     * @param  array<mixed> $packages Existing packages array to update.
     * @return array<mixed>
     */
    private function updateVuePackagesArray($packages)
    {
        return [
            'resolve-url-loader' => '^2.3.1',
            'sass' => '^1.20.1',
            'sass-loader' => '^8.0.0',
            'vue' => '^2.5.18',
            'vue-loader' => '^15.9.8',
            'vue-template-compiler' => '^2.6.10',
        ] + $packages;
    }

    /**
     * Update packages array for bootstrap.
     *
     * @param  array<mixed> $packages Existing packages array to update.
     * @return array<mixed>
     */
    private function updateBootstrapPackagesArray($packages)
    {
        return [
            'bootstrap' => '^5.0.0',
            'jquery' => '^3.6',
            '@popperjs/core' => '^2.9',
        ] + $packages;
    }

    /**
     * Update packages array for react.
     *
     * @param  array<mixed> $packages Existing packages array to update.
     * @return array<mixed>
     */
    private function updateReactPackagesArray($packages)
    {
        foreach ($packages as $packageName => $version) {
            if (in_array($packageName, ['vue', 'vue-template-compiler'])) {
                unset($packages[$packageName]);
            }
        }

        return [
            '@babel/preset-react' => '^7.0.0',
            'react' => '^16.2.0',
            'react-dom' => '^16.2.0',
            'bootstrap' => '^4.0.0',
            'jquery' => '^3.2',
            'popper.js' => '^1.12',
        ] + $packages;
    }

    /**
     * Update packages array for inertia-vue.
     *
     * @param  array<string> $packages Existing packages array to update.
     * @return array<string>
     */
    private function updateInertiavuePackagesArray($packages)
    {
        return [
            '@fullhuman/postcss-purgecss' => '^1.3.0',
            '@inertiajs/inertia' => '^0.1.7',
            '@inertiajs/inertia-vue' => '^0.1.2',
            'popper.js' => '^1.16.0',
            'portal-vue' => '^1.5.1',
            'vue' => '^2.6.11',
            'vue-meta' => '^2.3.1',
            'vue-loader' => '^15.9.8',
            'vue-template-compiler' => '^2.6.11',
            'bootstrap' => '^4.0.0',
        ] + $packages;
    }

    /**
     * Update packages array for inertia-react.
     *
     * @param  array<string> $packages Existing packages array to update.
     * @return array<string>
     */
    private function updateInertiareactPackagesArray($packages)
    {
        return [
            '@babel/preset-react' => '^7.0.0',
            '@inertiajs/inertia' => '^0.6.1',
            '@inertiajs/inertia-react' => '^0.4.1',
            'react-dom' => '^16.2.0',
            'react' => '^16.2.0',
        ] + $packages;
    }
}
