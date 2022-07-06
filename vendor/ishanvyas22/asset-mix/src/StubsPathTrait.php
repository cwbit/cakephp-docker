<?php
declare(strict_types=1);

namespace AssetMix;

use AssetMix\Command\AssetMixCommand;

trait StubsPathTrait
{
    /**
     * Returns base directory path of vue stubs path.
     *
     * @return string
     */
    public function getBaseCommonStubsPath(): string
    {
        return ASSET_MIX_ROOT . DS . 'stubs' . DS . 'common' . DS;
    }

    /**
     * Returns base directory path of vue stubs path.
     *
     * @return string
     */
    public function getBaseVueStubsPath(): string
    {
        return ASSET_MIX_ROOT . DS . 'stubs' . DS . 'vue' . DS;
    }

    /**
     * Returns base directory path of bootstrap stubs path.
     *
     * @return string
     */
    public function getBaseBootstrapStubsPath(): string
    {
        return ASSET_MIX_ROOT . DS . 'stubs' . DS . 'bootstrap' . DS;
    }

    /**
     * Returns base directory path of react stubs path.
     *
     * @return string
     */
    public function getBaseReactStubsPath(): string
    {
        return ASSET_MIX_ROOT . DS . 'stubs' . DS . 'react' . DS;
    }

    /**
     * Returns base directory path of `inertia-vue` stubs path.
     *
     * @return string
     */
    public function getBaseInertiaVueStubsPath(): string
    {
        return ASSET_MIX_ROOT . DS . 'stubs' . DS . 'inertia-vue' . DS;
    }

    /**
     * Returns base directory path of `inertia-react` stubs path.
     *
     * @return string
     */
    public function getBaseInertiaReactStubsPath(): string
    {
        return ASSET_MIX_ROOT . DS . 'stubs' . DS . 'inertia-react' . DS;
    }

    /**
     * Returns `package.json` file paths for vue.
     *
     * @return array<string>
     */
    public function getVuePackageJsonPath(): array
    {
        $packageJsonPath = $this->getBaseCommonStubsPath() . 'package.json';

        return [
            'from' => $packageJsonPath,
            'to' => ROOT . DS . basename($packageJsonPath),
        ];
    }

    /**
     * Returns `package.json` file paths for bootstrap.
     *
     * @return array<string>
     */
    public function getBootstrapPackageJsonPath(): array
    {
        $packageJsonPath = $this->getBaseCommonStubsPath() . 'package.json';

        return [
            'from' => $packageJsonPath,
            'to' => ROOT . DS . basename($packageJsonPath),
        ];
    }

    /**
     * Returns `package.json` file paths for react.
     *
     * @return array<string>
     */
    public function getReactPackageJsonPath(): array
    {
        $packageJsonPath = $this->getBaseCommonStubsPath() . 'package.json';

        return [
            'from' => $packageJsonPath,
            'to' => ROOT . DS . basename($packageJsonPath),
        ];
    }

    /**
     * Returns `package.json` file paths for inertia-vue.
     *
     * @return array<string>
     */
    public function getInertiaVuePackageJsonPath(): array
    {
        $packageJsonPath = $this->getBaseCommonStubsPath() . 'package.json';

        return [
            'from' => $packageJsonPath,
            'to' => ROOT . DS . basename($packageJsonPath),
        ];
    }

    /**
     * Returns `package.json` file paths for inertia-react.
     *
     * @return array<string>
     */
    public function getInertiaReactPackageJsonPath(): array
    {
        $packageJsonPath = $this->getBaseCommonStubsPath() . 'package.json';

        return [
            'from' => $packageJsonPath,
            'to' => ROOT . DS . basename($packageJsonPath),
        ];
    }

    /**
     * Returns `webpack.mix.js` file path for vue.
     *
     * @return array<string>
     */
    public function getVueWebpackMixJsPath(): array
    {
        $webpackConfigPath = $this->getBaseVueStubsPath() . 'webpack.mix.js';

        return [
            'from' => $webpackConfigPath,
            'to' => basename($webpackConfigPath),
        ];
    }

    /**
     * Returns `webpack.mix.js` file path for bootstrap.
     *
     * @return array<string>
     */
    public function getBootstrapWebpackMixJsPath(): array
    {
        $webpackConfigPath = $this->getBaseCommonStubsPath() . 'webpack.mix.js';

        return [
            'from' => $webpackConfigPath,
            'to' => basename($webpackConfigPath),
        ];
    }

    /**
     * Returns `webpack.mix.js` file path for react.
     *
     * @return array<string>
     */
    public function getReactWebpackMixJsPath(): array
    {
        $webpackConfigPath = $this->getBaseReactStubsPath() . 'webpack.mix.js';

        return [
            'from' => $webpackConfigPath,
            'to' => basename($webpackConfigPath),
        ];
    }

    /**
     * Returns `webpack.mix.js` file path for inertia-vue.
     *
     * @return array<string>
     */
    public function getInertiaVueWebpackMixJsPath(): array
    {
        $webpackConfigPath = $this->getBaseInertiaVueStubsPath() . 'webpack.mix.js';

        return [
            'from' => $webpackConfigPath,
            'to' => basename($webpackConfigPath),
        ];
    }

    /**
     * Returns `webpack.mix.js` file path for inertia-react.
     *
     * @return array<string>
     */
    public function getInertiaReactWebpackMixJsPath(): array
    {
        $webpackConfigPath = $this->getBaseInertiaReactStubsPath() . 'webpack.mix.js';

        return [
            'from' => $webpackConfigPath,
            'to' => basename($webpackConfigPath),
        ];
    }

    /**
     * Returns paths of `assets` directory files for vue.
     *
     * @param string|null $dirname Custom directory name.
     * @return array<string>
     */
    public function getVueAssetsDirPaths($dirname = null): array
    {
        if ($dirname === null) {
            $dirname = AssetMixCommand::ASSETS_DIR_NAME;
        }

        $assetsDirPath = $this->getBaseVueStubsPath() . $dirname;

        return [
            'from_assets' => $assetsDirPath,
            'to_assets' => basename($assetsDirPath),
            'to_assets_css' => basename($assetsDirPath) . DS . 'css',
            'to_assets_js' => basename($assetsDirPath) . DS . 'js',
            'to_assets_js_app' => basename($assetsDirPath) . DS . 'js' . DS . 'app.js',
            'to_assets_js_components' => basename($assetsDirPath) . DS . 'js' . DS . 'components',
            'to_assets_sass' => basename($assetsDirPath) . DS . 'sass',
            'to_assets_sass_app' => basename($assetsDirPath) . DS . 'sass' . DS . 'app.scss',
        ];
    }

    /**
     * Returns paths of `assets` directory files for inertia-vue.
     *
     * @param string|null $dirname Custom directory name.
     * @return array<string>
     */
    public function getInertiaVueAssetsDirPaths($dirname = null): array
    {
        if ($dirname === null) {
            $dirname = AssetMixCommand::ASSETS_DIR_NAME;
        }

        $assetsDirPath = $this->getBaseInertiaVueStubsPath() . $dirname;

        return [
            'from_assets' => $assetsDirPath,
            'to_assets' => basename($assetsDirPath),
            'to_assets_css' => basename($assetsDirPath) . DS . 'css',
            'to_assets_js' => basename($assetsDirPath) . DS . 'js',
            'to_assets_js_app' => basename($assetsDirPath) . DS . 'js' . DS . 'app.js',
            'to_assets_sass' => basename($assetsDirPath) . DS . 'sass',
            'to_assets_sass_app' => basename($assetsDirPath) . DS . 'sass' . DS . 'app.scss',
        ];
    }

    /**
     * Returns paths of `assets` directory files for inertia-react.
     *
     * @param string|null $dirname Custom directory name.
     * @return array<string>
     */
    public function getInertiaReactAssetsDirPaths($dirname = null): array
    {
        if ($dirname === null) {
            $dirname = AssetMixCommand::ASSETS_DIR_NAME;
        }

        $assetsDirPath = $this->getBaseInertiaReactStubsPath() . $dirname;

        return [
            'from_assets' => $assetsDirPath,
            'to_assets' => basename($assetsDirPath),
            'to_assets_css' => basename($assetsDirPath) . DS . 'css',
            'to_assets_js' => basename($assetsDirPath) . DS . 'js',
            'to_assets_js_app' => basename($assetsDirPath) . DS . 'js' . DS . 'app.js',
            'to_assets_sass' => basename($assetsDirPath) . DS . 'sass',
            'to_assets_sass_app' => basename($assetsDirPath) . DS . 'sass' . DS . 'app.scss',
        ];
    }

    /**
     * Returns paths of `assets` directory files for bootstrap.
     *
     * @param string|null $dirname Custom directory name.
     * @return array<string>
     */
    public function getBootstrapAssetsDirPaths($dirname = null): array
    {
        if ($dirname === null) {
            $dirname = AssetMixCommand::ASSETS_DIR_NAME;
        }

        $assetsDirPath = $this->getBaseBootstrapStubsPath() . $dirname;

        return [
            'from_assets' => $assetsDirPath,
            'to_assets' => basename($assetsDirPath),
            'to_assets_css' => basename($assetsDirPath) . DS . 'css',
            'to_assets_js' => basename($assetsDirPath) . DS . 'js',
            'to_assets_js_app' => basename($assetsDirPath) . DS . 'js' . DS . 'app.js',
            'to_assets_sass' => basename($assetsDirPath) . DS . 'sass',
            'to_assets_sass_app' => basename($assetsDirPath) . DS . 'sass' . DS . 'app.scss',
        ];
    }

    /**
     * Returns paths of `assets` directory files for react.
     *
     * @param string|null $dirname Custom directory name.
     * @return array<string>
     */
    public function getReactAssetsDirPaths($dirname = null): array
    {
        if ($dirname === null) {
            $dirname = AssetMixCommand::ASSETS_DIR_NAME;
        }

        $assetsDirPath = $this->getBaseReactStubsPath() . $dirname;

        return [
            'from_assets' => $assetsDirPath,
            'to_assets' => basename($assetsDirPath),
            'to_assets_css' => basename($assetsDirPath) . DS . 'css',
            'to_assets_js' => basename($assetsDirPath) . DS . 'js',
            'to_assets_js_app' => basename($assetsDirPath) . DS . 'js' . DS . 'app.js',
            'to_assets_sass' => basename($assetsDirPath) . DS . 'sass',
            'to_assets_sass_app' => basename($assetsDirPath) . DS . 'sass' . DS . 'app.scss',
        ];
    }
}
