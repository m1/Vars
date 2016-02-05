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
 * This file provides the loaders and extensions supported by Vars
 *
 * @since 0.1.1
 */
class LoaderProvider
{
    /**
     * The available extensions
     *
     * @var array $extensions
     */
    private $extensions = array();

    /**
     * The available loaders
     *
     * @var array $loaders
     */
    private $loaders = array();

    /**
     * The constructor for LoaderProvider, makes the loaders and extensions
     *
     * @param array|null $options           The options being used for Vars
     * @param array      $default_loaders   The default loaders for Vars
     */
    public function __construct($options, $default_loaders)
    {
        $this->makeLoaders($options, $default_loaders);
    }

    /**
     * Get loaders and make extensions for the loaders
     *
     * @param array|null $options           The options being used for Vars
     * @param array      $default_loaders   The default loaders for Vars
     */
    private function makeLoaders($options, $default_loaders)
    {
        $loaders = $this->preParseLoaders($options, $default_loaders);
        $parsed_loaders = array();

        foreach ($loaders as $loader) {
            if ($loader === 'default') {
                $parsed_loaders = array_merge($parsed_loaders, $default_loaders);
            } else {
                $parsed_loaders[] = $loader;
            }
        }

        $parsed_loaders = array_unique($parsed_loaders);

        $this->loaders = $this->makeNameSpaceLoaders($parsed_loaders, $default_loaders);
        $this->extensions = $this->makeExtensions($this->loaders);
    }

    /**
     * Pre parse the loaders for use in make loaders
     *
     * @param array|null $options           The options being used for Vars
     * @param array      $default_loaders   The default loaders for Vars
     *
     * @return array The pre parsed loaders
     */
    private function preParseLoaders($options, $default_loaders)
    {
        $loaders = array();

        if (is_array($options['loaders']) && !empty($options['loaders'])) {
            $loaders = $options['loaders'];
        } elseif (is_string($options['loaders'])) {
            $loaders[] = $options['loaders'];
        } else {
            $loaders = $default_loaders;
        }

        return $loaders;
    }

    /**
     * Makes namespace loaders from loader strings
     *
     * @param array $loaders The options being used for Vars
     * @param array      $default_loaders   The default loaders for Vars
     *
     * @throws \InvalidArgumentException If a loader from options isn't found
     *
     * @return array The namespace loaders
     */
    private function makeNameSpaceLoaders($loaders, $default_loaders)
    {
        $parsed_loaders = array();

        foreach ($loaders as $loader) {
            if (in_array($loader, $default_loaders)) {
                $loader = sprintf('%s\%sLoader', __NAMESPACE__, ucfirst(strtolower($loader)));
            }

            if (!class_exists($loader)) {
                throw new \InvalidArgumentException(sprintf("'%s' loader class does not exist", $loader));
            }

            $parsed_loaders[] = $loader;
        }

        return $parsed_loaders;
    }

    /**
     * Get and make extensions for loaders made from makeLoaders()
     *
     * @see \M1\Vars\Vars::makeLoaders() \M1\Vars\Vars::makeLoaders()
     *
     * @param  array $loaders File loaders
     *
     * @throws \RuntimeException If no loader extensions were found
     *
     * @return array File loader supported extensions
     */
    private function makeExtensions(array $loaders)
    {
        $extensions = array();

        foreach ($loaders as $loader) {
            $extensions = array_merge($extensions, $loader::$supported);
        }

        if (empty($extensions)) {
            throw new \RuntimeException('No loader extensions were found');
        }

        return $extensions;
    }

    /**
     * Get the Vars file loaders
     *
     * @return array The Vars file loaders
     */
    public function getLoaders()
    {
        return $this->loaders;
    }

    /**
     * Get the Vars file loaders extensions
     *
     * @return array The Vars file loader extensions
     */
    public function getExtensions()
    {
        return $this->extensions;
    }
}
