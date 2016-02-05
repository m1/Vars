<?php

/**
 * This file is part of the m1\vars library
 *
 * (c) m1 <hello@milescroxford.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     m1/vars
 * @version     1.1.0
 * @author      Miles Croxford <hello@milescroxford.com>
 * @copyright   Copyright (c) Miles Croxford <hello@milescroxford.com>
 * @license     http://github.com/m1/vars/blob/master/LICENSE
 * @link        http://github.com/m1/vars/blob/master/README.MD Documentation
 */

namespace M1\Vars\Loader;

/**
 * This file provides dir loading support for Vars
 *
 * @since 0.1.1
 */
class DirectoryLoader extends AbstractLoader
{
    /**
     * Construct the loader with the passed entity
     *
     * @param string $entity    The passed entity
     * @param bool   $recursive Search the directories   recursively
     */
    public function __construct($entity, $recursive)
    {
        parent::__construct($entity);
        $this->recursive = $recursive;
    }

    /**
     * {@inheritdoc}
     */
    public function load()
    {
        $paths = array();
        $files = ($this->recursive) ? $this->getSupportedFilesRecursively() : $this->getSupportedFiles();

        foreach ($files as $path => $file) {
            if ($file->isFile()) {
                $paths[] = $path;
            }
        }
        $this->content = $this->makeResources($paths);

        return $this;
    }

    /**
     * Returns the supported files in the directory recursively
     *
     * @return \RegexIterator The supported files in the directories
     */
    private function getSupportedFilesRecursively()
    {
        $dir_it = new \RecursiveDirectoryIterator($this->entity, \RecursiveDirectoryIterator::SKIP_DOTS);

        $files = new \RecursiveIteratorIterator(
            $dir_it,
            \RecursiveIteratorIterator::LEAVES_ONLY,
            \RecursiveIteratorIterator::CATCH_GET_CHILD
        );

        return $this->createRegexIterator($files);
    }

    /**
     * Returns the supported files in the directory
     *
     * @return \RegexIterator The supported files in the directory
     */
    private function getSupportedFiles()
    {
        $files = new \FilesystemIterator($this->entity);

        return $this->createRegexIterator($files);
    }

    /**
     * Returns the supported files in the directory
     *
     * @param \FilesystemIterator|\RecursiveIteratorIterator $files The found files in the directory/ies
     *
     * @return \RegexIterator The supported files in the directory using the regexiterator
     */
    private function createRegexIterator($files)
    {
        return new \RegexIterator(
            $files,
            '/^.*\.(' . implode('|', static::$supported) . ')$/i'
        );
    }

    /**
     * Makes usable resource paths from path strings
     *
     * @param array $paths The path strings
     *
     * @return array|bool  The usable resources if any, else false
     */
    private function makeResources($paths)
    {
        if ($paths && !empty($paths)) {
            $resources = array();

            foreach ($paths as $path) {
                $resources[] = $path;
            }

            return $resources;
        }

        return false;
    }
}
