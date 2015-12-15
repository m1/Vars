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
 * @version     0.2.0
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
     * {@inheritdoc}
     */
    public function load()
    {
        $paths = array();

        foreach ($this->getSupportedFiles() as $path => $file) {
            if ($file->isFile()) {
                $paths[] = $path;
            }
        }
        $this->content = $this->makeResources($paths);

        return $this;
    }

    /**
     * Returns the supported files in the directory
     *
     * @return array The supported files in the directory
     */
    private function getSupportedFiles()
    {
        $dir_it = new \RecursiveDirectoryIterator($this->entity, \RecursiveDirectoryIterator::SKIP_DOTS);

        $dir_files = new \RecursiveIteratorIterator(
            $dir_it,
            \RecursiveIteratorIterator::LEAVES_ONLY,
            \RecursiveIteratorIterator::CATCH_GET_CHILD
        );

        $supported_files = new \RegexIterator(
            $dir_files,
            '/^.*\.(' . implode('|', static::$supported) . ')$/i'
        );

        return $supported_files;
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
