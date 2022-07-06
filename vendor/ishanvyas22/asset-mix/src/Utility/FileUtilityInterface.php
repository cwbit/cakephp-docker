<?php
declare(strict_types=1);

namespace AssetMix\Utility;

/**
 * Contract for fily utility class
 */
interface FileUtilityInterface
{
    /**
     * Copy files or directories to new location
     *
     * @param string $from Path of a file/directory
     * @param string $to Path of new location
     * @return bool
     */
    public function copy($from, $to);

    /**
     * Copy files and directories recursively.
     *
     * @param string $source Source path to copy from.
     * @param string $destination Destination path to copy to.
     * @return void
     */
    public function recursiveCopy($source, $destination);

    /**
     * Checks if give file or directory exists
     *
     * @param string $path Location of a file/directory
     * @return bool
     */
    public function exists($path);

    /**
     * Create new directory
     *
     * @param string $path Location of a directory
     * @param array<string> $options Configuration options
     * @return bool
     */
    public function mkdir($path, $options = []);

    /**
     * Remove(delete) files or directories
     *
     * @param string|array<string> $paths Path of a file/directory to delete
     * @return void
     */
    public function delete($paths);

    /**
     * Writes into a file with give contents.
     * Creates file if not exist.
     *
     * @param string $filename File name with absolute path.
     * @param string $content Content to write into the file.
     * @return bool Returns true on success, false otherwise.
     * @throws \Exception In case of failure.
     */
    public function write($filename, $content);
}
