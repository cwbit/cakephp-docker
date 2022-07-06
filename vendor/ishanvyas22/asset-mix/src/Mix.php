<?php
declare(strict_types=1);

namespace AssetMix;

use Exception;

/**
 * Paths related to mix files, generate by laravel mix.
 */
class Mix
{
    /**
     * Mix manifests array
     *
     * @var array<string>
     */
    private static $manifests = [];

    /**
     * Get the path to a versioned Mix file.
     *
     * @param string $path Path of the asset file.
     * @param string $manifestDirectory Custom manifest directory.
     * @return string
     * @throws \Exception
     */
    public function __invoke($path, $manifestDirectory = ''): string
    {
        $urlDomain = '';
        // remove the scheme and domain from the front of the path if it has one
        // https://regex101.com/r/FyT9T4/1
        if (preg_match('@^([a-z]+://[^/]+)(/.+)$@', $path, $matches)) {
            $urlDomain = $matches[1];
            $path = $matches[2];
        }

        if (! starts_with($path, '/')) {
            $path = "/{$path}";
        }

        if ($manifestDirectory && ! starts_with($manifestDirectory, '/')) {
            $manifestDirectory = "/{$manifestDirectory}";
        }

        if (file_exists(WWW_ROOT . $manifestDirectory . '/hot')) {
            $content = file_get_contents(WWW_ROOT . $manifestDirectory . '/hot');

            if ($content === false) {
                throw new \Exception('Invalid manifest directory contents');
            }

            $url = rtrim($content);
            if (starts_with($url, ['http://', 'https://'])) {
                return str_after($url, ':') . $path;
            }

            return "//localhost:8765{$path}";
        }

        $manifestPath = WWW_ROOT . $manifestDirectory . '/mix-manifest.json';
        if (! isset(self::$manifests[$manifestPath])) {
            if (! file_exists($manifestPath)) {
                throw new Exception('The Mix manifest does not exist.');
            }

            $manifestFileContent = file_get_contents($manifestPath);

            if ($manifestFileContent === false) {
                throw new Exception('The Mix manifest file content is not valid.');
            }

            self::$manifests[$manifestPath] = json_decode($manifestFileContent, true);
        }

        $manifest = self::$manifests[$manifestPath];
        if (! isset($manifest[$path])) {
            throw new Exception("Unable to locate AssetMix file: {$path}.");
        }

        return $urlDomain . $manifestDirectory . $manifest[$path];
    }

    /**
     * Reset manifests array
     *
     * @return void
     */
    public static function reset(): void
    {
        self::$manifests = [];
    }
}
