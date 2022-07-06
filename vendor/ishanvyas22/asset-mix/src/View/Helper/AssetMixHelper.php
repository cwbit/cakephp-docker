<?php
declare(strict_types=1);

namespace AssetMix\View\Helper;

use AssetMix\Mix;
use Cake\View\Helper;

/**
 * AssetMix helper
 *
 * @property \Cake\View\Helper\HtmlHelper $Html
 * @property \Cake\View\Helper\UrlHelper $Url
 */
class AssetMixHelper extends Helper
{
    /**
     * List of helpers used by this helper
     *
     * @var array<string>
     */
    protected $helpers = ['Html', 'Url'];

    /**
     * Creates a link element for CSS stylesheets with versioned asset.
     *
     * @param string $path Path to css file.
     * @param array<mixed> $options Options array.
     * @return string|null CSS `<link />` or `<style />` tag, depending on the type of link.
     */
    public function css(string $path, array $options = []): ?string
    {
        // Get css file path, add extension if not provided, skip if url provided
        if (strpos($path, '//') !== false) {
            return $this->Html->css($path, $options);
        }

        $url = $this->Url->css($path, $options);

        // Pass proper filename with path to mix common function
        $mixPath = (new Mix())($url);

        return $this->Html->css($mixPath, $options);
    }

    /**
     * Returns one or many `<script>` tags depending on the number of scripts given.
     *
     * @param string $url String or array of javascript files to include
     * @param array<mixed> $options Array of options, and html attributes see above.
     * @return string|null String of `<script />` tags or null if block is specified in options
     *   or if $once is true and the file has been included before.
     */
    public function script(string $url, array $options = []): ?string
    {
        $defaults = ['defer' => true];
        $options += $defaults;

        // Get css file path, add extension if not provided, skip if url provided
        if (strpos($url, '//') !== false) {
            return $this->Html->script($url, $options);
        }

        $url = $this->Url->script($url, $options);

        // Pass proper filename with path to mix common function
        $mixPath = (new Mix())($url);

        return $this->Html->script($mixPath, $options);
    }
}
