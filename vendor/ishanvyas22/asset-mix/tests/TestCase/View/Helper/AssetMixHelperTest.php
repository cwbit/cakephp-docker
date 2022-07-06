<?php
declare(strict_types=1);

namespace AssetMix\Test\TestCase\View\Helper;

use AssetMix\Mix;
use AssetMix\View\Helper\AssetMixHelper;
use Cake\Core\Configure;
use Cake\Filesystem\Folder;
use Cake\TestSuite\TestCase;
use Cake\View\View;

/**
 * AssetMix\View\Helper\AssetMixHelper Test Case
 */
class AssetMixHelperTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \AssetMix\View\Helper\AssetMixHelper
     */
    public $AssetMix;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        mkdir(TEST_APP_DIR . 'webroot');

        $view = new View();
        $this->AssetMix = new AssetMixHelper($view);
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        unset($this->AssetMix);

        parent::tearDown();

        $dir = new Folder(TEST_APP_DIR . 'webroot');
        $dir->delete();

        $this->_cleanUp();

        Mix::reset();
    }

    /**
     * Copy `mix-manifest.json` file to `test_app` webroot
     *
     * @return void
     */
    protected function _copy($withVersion = false)
    {
        $sourceFilename = 'mix-manifest.json';
        $destinationFilename = 'mix-manifest.json';

        if ($withVersion) {
            $sourceFilename = 'mix-manifest-with-version.json';
        }

        if (! copy(COMPARE_PATH . $sourceFilename, WWW_ROOT . $destinationFilename)) {
            throw new \Exception('Unable to copy mix-manifest.json file');
        }
    }

    /**
     * Copy `mix-manifest.json` file to `test_app` webroot
     *
     * @return void
     */
    protected function _copyWithoutVersion()
    {
        $this->_copy(false);
    }

    /**
     * Copy `mix-manifest-with-version.json` file to `test_app` webroot
     *
     * @return void
     */
    protected function _copyWithVersion()
    {
        $this->_copy(true);
    }

    /**
     * Clean webroot directory
     *
     * @return void
     */
    protected function _cleanUp()
    {
        $files = glob(WWW_ROOT . '*');

        foreach ($files as $file) {
            if (! is_file($file)) {
                continue;
            }

            unlink($file);
        }
    }

    /**
     * Test `css()` function returns proper tag without versioning
     *
     * @return void
     */
    public function testStyleTagWithoutVersion()
    {
        $this->_copyWithoutVersion();

        $result = $this->AssetMix->css('main');

        $this->assertStringContainsString('<link', $result);
        $this->assertStringContainsString('rel="stylesheet"', $result);
        $this->assertStringContainsString('href="/css/main.css"', $result);
    }

    /**
     * Test `script()` function returns proper tag without versioning
     *
     * @return void
     */
    public function testScriptTagWithoutVersion()
    {
        $this->_copyWithoutVersion();

        $result = $this->AssetMix->script('app');

        $this->assertStringContainsString('<script', $result);
        $this->assertStringContainsString('/js/app.js', $result);
        $this->assertStringContainsString('defer="defer"', $result);
    }

    /**
     * Test `css()` function returns proper tag
     * with versioning enabled
     *
     * @return void
     */
    public function testStyleTagWithVersion()
    {
        $this->_copyWithVersion();

        $result = $this->AssetMix->css('main');

        $this->assertStringContainsString('<link', $result);
        $this->assertStringContainsString('rel="stylesheet"', $result);
        $this->assertStringContainsString('href="/css/main.css?id=9c4259d5465e35535a2a"', $result);
    }

    /**
     * Test `script()` function returns proper tag
     * with versioning enabled
     *
     * @return void
     */
    public function testScriptTagWithVersion()
    {
        $this->_copyWithVersion();

        $result = $this->AssetMix->script('app');

        $this->assertStringContainsString('<script', $result);
        $this->assertStringContainsString('/js/app.js?id=f059fcadc7eba26be9ae', $result);
        $this->assertStringContainsString('defer="defer"', $result);
    }

    /**
     * Test `script()` function returns proper tag
     * without defer option
     *
     * @return void
     */
    public function testScriptTagWithoutDefer()
    {
        $this->_copyWithVersion();

        $result = $this->AssetMix->script('app', ['defer' => false]);

        $this->assertStringContainsString('<script', $result);
        $this->assertStringContainsString('/js/app.js?id=f059fcadc7eba26be9ae', $result);
        $this->assertStringNotContainsString('defer', $result);
    }

    /**
     * Test `css()` function returns proper url when using a CDN
     *
     * @return void
     */
    public function testStyleTagWithExternalBaseUrl()
    {
        Configure::write('App.cssBaseUrl', 'https://example.com/css/');

        $this->_copyWithoutVersion();

        $result = $this->AssetMix->css('main');

        $this->assertStringContainsString('https://example.com/css/main.css', $result);
    }

    /**
     * Test `script()` function returns proper url when using a CDN
     *
     * @return void
     */
    public function testScriptTagWithExternalBaseUrl()
    {
        Configure::write('App.jsBaseUrl', 'https://example.com/js/');

        $this->_copyWithoutVersion();

        $result = $this->AssetMix->script('app');

        $this->assertStringContainsString('https://example.com/js/app.js', $result);
    }
}
