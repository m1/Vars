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

namespace M1\Vars\Traits;

/**
 * File trait gives common operation functions needed for files in Vars
 *
 * @since 0.1.0
 */
trait FileTrait
{
    /**
     * The file used in the trait
     *
     * @var string
     */
    private $file;

    /**
     * The parent Vars instance
     *
     * @var \M1\Vars\Vars
     */
    private $vars;

    /**
     * Validates the file passed to see if it is a file and is readable
     *
     * @throws \RuntimeException If the file passed is not a file
     * @throws \RuntimeException If the file passed is not readable
     */
    private function validate()
    {
        $file = $this->file;

        if (!is_file($file)) {
            throw new \RuntimeException(sprintf("'%s' is not a file", $file));
        }

        if (!is_readable($file)) {
            // @codeCoverageIgnoreStart
            throw new \RuntimeException(sprintf("'%s' is not a readable file", $file));
            // @codeCoverageIgnoreEnd
        }
    }

    /**
     * Gets the supported loader for the passed file
     *
     * @see \M1\Vars\Vars::getLoaders() \M1\Vars\Vars::getLoaders()
     *
     * @param string $data The passed file
     *
     * @return mixed Returns the loader class or false if no loader calls was found
     */
    private function getSupportedLoader($data)
    {
        $loaders = $this->vars->loader->getLoaders();

        foreach ($loaders as $loader) {
            $class_loader = new \ReflectionClass($loader);
            $class_loader = $class_loader->newInstanceArgs(array($data));

            if ($class_loader->supports()) {
                return $class_loader;
            }
        }
        return false;
    }

    /**
     * Loads raw content from the file
     *
     * @param string $data The passed file
     *
     * @throws \InvalidArugmentException If the file passed is not supported by the current loaders
     *
     * @return mixed The content from the file via the loader
     */
    private function loadContent($data)
    {
        $loader = $this->getSupportedLoader($data);

        if (!$loader) {
            throw new \InvalidArgumentException(sprintf("'%s' is not supported by the current loaders", $this->file));
        }

        $loader->load();
        return $loader->getContent();
    }

    /**
     * Returns the passed file
     *
     * @return string The passed file
     */
    public function getFile()
    {
        return $this->file;
    }
}
