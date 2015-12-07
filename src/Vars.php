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
 * @version     0.1.0
 * @author      Miles Croxford <hello@milescroxford.com>
 * @copyright   Copyright (c) Miles Croxford <hello@milescroxford.com>
 * @license     http://github.com/m1/vars/blob/master/LICENSE
 * @link        http://github.com/m1/vars/blob/master/README.MD Documentation
 */

namespace M1\Vars;

use M1\Vars\Resource\AbstractResource;
use M1\Vars\Resource\ResourceProvider;
use M1\Vars\Resource\VariableResource;

/**
 * Vars core class
 *
 * @since 0.1.0
 */
class Vars extends AbstractResource
{
    /**
     * The base path for the Vars config and cache folders
     *
     * @var string $base_path
     */
    private $base_path;

    /**
     * The base path for the Vars config and cache folders
     *
     * @var boolean $cache
     */
    private $cache;

    /**
     * Has the cache been attempted
     *
     * @var bool $cache_attempted
     */
    private $cache_attempted = false;

    /**
     * How long for the cache to be fresh
     *
     * @var int $cache_expire
     */
    private $cache_expire;

    /**
     * Has the cache been loaded
     *
     * @var bool $cache_loaded
     */
    private $cache_loaded = false;

    /**
     * The cache file name
     *
     * @var string $cache_name
     */
    private $cache_name;

    /**
     * The specific path for the cache folder
     *
     * @var string $cache_path
     */
    private $cache_path;

    /**
     * If cached, the time when the class was cached
     *
     * @var int $cache_time
     */
    private $cache_time;

    /**
     * The available extensions
     *
     * @var array $extensions
     */
    private $extensions = array();

    /**
     * The default options for Vars
     *
     * @var array $default_options
     */
    private $default_options = array(
        'base_path' => null,
        'cache' => true,
        'cache_path' => null,
        'cache_expire' => 300, // 5 minutes
        'loaders' => array('ini', 'json', 'php', 'toml', 'yaml', 'xml',)
    );

    /**
     * The available loaders
     *
     * @var array $loaders
     */
    private $loaders = array();

    /**
     * Have the base and cache paths been set
     *
     * @var bool $paths_loaded
     */
    private $paths_loaded = false;

    /**
     * The imported resources
     *
     * @var array $resources
     */
    private $resources = array();

    /**
     * The words to be replaced in the config files
     *
     * @var array $variables
     */
    private $variables = array();

    /**
     * Creates a new instance of Vars
     *
     * @param string|array $resource The main configuration resource
     * @param array        $options  The options being used for Vars
     */
    public function __construct($resource, $options = null)
    {
        $this->makePaths($options);
        $this->makeCache($options, $resource);

        if (!$this->checkCache()) {
            $this->makeLoaders($options);
            $this->makeVariables($options);

            $resource = new ResourceProvider($this, $resource);
        }

        if ($this->cache_loaded) {
            $this->loadFromCache();
        } else {
            $resource->mergeParentContent();
            $this->content = $resource->getContent();

            if ($this->cache) {
                $this->cache_time = time();
                $cache_file = sprintf('%s/%s.php', $this->cache_path, $this->cache_name);
                file_put_contents($cache_file, serialize($this));
            }
        }
    }

    /**
     * Sets the base path if the options have been set and the cache path if the base path has been set
     *
     * @param array|null $options The options being used for Vars
     */
    private function makePaths($options)
    {
        $bp = false;
        $cp = false;

        if (isset($options['base_path']) && $options['base_path']) {
            $bp = true;
            $this->setBasePath($options['base_path']);
        }

        if (isset($options['cache_path']) && $options['cache_path']) {
            $cp = true;
            $this->setCachePath($options['cache_path']);
        } elseif ($bp) {
            $cp = true;
            $this->setCachePath($options['base_path']);
        }

        if ($bp && $cp) {
            $this->paths_loaded = true;
        } elseif ($bp) {
            $this->cache_path = false;
        } elseif ($cp) {
            $this->base_path = false;
        }
    }

    /**
     * Sets the cache options
     *
     * @param array|null   $options  The options being used for Vars
     * @param array|string $resource The main configuration resource
     */
    private function makeCache($options, $resource)
    {
        if (isset($options['cache'])) {
            $cache = $options['cache'];
        } else {
            $cache = $this->default_options['cache'];
        }

        $cache_name = md5(serialize($resource));

        if ($cache) {
            if (isset($options['cache_expire']) && is_int($options['cache_expire'])) {
                $this->cache_expire = $options['cache_expire'];
            } else {
                $this->cache_expire = $this->default_options['cache_expire'];
            }

        } else {
            $this->cache_expire = false;
        }

        $this->cache = $cache;
        $this->cache_name = $cache_name;
    }

    /**
     * Checks the cache to see if there is a valid cache available
     *
     * @return bool Returns true if has the cached resource
     */
    public function checkCache()
    {
        if ($this->cache && $this->cache_path && !$this->cache_attempted) {
            $file = sprintf('%s/%s.php', $this->cache_path, $this->cache_name);
            $this->cache_attempted = true;

            if (is_file($file) &&
                filemtime($file) >= (time() - $this->cache_expire)) {
                    $this->cache_loaded = true;
                    return true;
            }
        }

        return false;
    }

    /**
     * Get loaders and make extensions for the loaders
     *
     * @param array|null $options The options being used for Vars
     *
     * @throws \InvalidArgumentException If a loader from options isn't found
     * @throws \InvalidArgumentException If no loaders were loaded
     */
    private function makeLoaders($options)
    {
        $loaders = array();
        $default_loaders = $this->default_options['loaders'];

        if (isset($options['loaders']) && !is_null($options['loaders']) && !empty($options['loaders'])) {
            if (is_array($options['loaders'])) {
                $loaders = $options['loaders'];
            } elseif (is_string($options['loaders'])) {
                $loaders[] = $options['loaders'];
            }
        } else {
            $loaders = $default_loaders;
        }

        $parsed_loaders = array();

        foreach ($loaders as $loader) {
            if ($loader === 'default') {
                $parsed_loaders = array_merge($parsed_loaders, $default_loaders);
            } else {
                $parsed_loaders[] = $loader;
            }
        }

        $parsed_loaders = array_unique($parsed_loaders);
        $namespaced_loaders = array();

        foreach ($parsed_loaders as $loader) {
            if (in_array($loader, $default_loaders)) {
                $loader = sprintf('%s\Loader\%sLoader', __NAMESPACE__, ucfirst(strtolower($loader)));
            }

            if (!class_exists($loader)) {
                throw new \InvalidArgumentException(sprintf("'%s' loader class does not exist", $loader));
            }

            $namespaced_loaders[] = $loader;
        }

        if (!$namespaced_loaders) {
            throw new \InvalidArgumentException('No loaders were loaded');
        }

        $this->loaders = $namespaced_loaders;
        $this->extensions = $this->makeExtensions($namespaced_loaders);
    }

    /**
     * Sets the replacement variables if the option has been set
     *
     * @param array|null $options The options being used for Vars
     */
    private function makeVariables($options)
    {
        if (isset($options['variables'])) {
            $variables = new VariableResource($this, $options['variables']);

            $v = array();

            foreach ($variables->getVariables() as $variable_key => $variable_value) {
                $v["%".$variable_key.'%'] = $variable_value;
            }

            $this->variables = $v;
        }
    }

    /**
     * Loads the cached file into the current class
     */
    private function loadFromCache()
    {
        $cached_file = sprintf('%s/%s.php', $this->cache_path, $this->cache_name);
        $cached_config = unserialize(file_get_contents($cached_file));

        $passed_keys = array(
            'base_path',
            'cache_name',
            'cache_path',
            'cache_time',
            'content',
            'extensions',
            'loaders',
            'resources',
            'variables',
        );

        foreach (get_object_vars($cached_config) as $key => $value) {
            if (in_array($key, $passed_keys)) {
                $this->$key = $value;
            }
        }
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

        if (!$extensions) {
            throw new \RuntimeException('No loader extensions were found');
        }

        return $extensions;
    }

    /**
     * Checks if the base and cache paths have been set, if not set then will use the $resource as the base path
     *
     * @param string $resource The resource to use to set the paths if they haven't been set
     */
    public function pathsLoadedCheck($resource)
    {
        if (!$this->paths_loaded) {
            $base_path = $this->getBasePath();

            if (!$base_path) {
                $file = pathinfo(realpath($resource));
                $base_path = $file['dirname'];
                $this->setBasePath($base_path);
            }

            $cache = $this->getCache();
            
            if ($cache) {
                $cache_path = $this->getCachePath();
                
                if (!$cache_path) {
                    $cache_path = $base_path;
                    $this->setCachePath($cache_path);
                }
            }

            $this->paths_loaded = true;
        }
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

    /**
     * Get the Vars base path
     *
     * @return string The Vars base path
     */
    public function getBasePath()
    {
        return $this->base_path;
    }

    /**
     * Set the Vars base path
     *
     * @param string $base_path The base path to set
     *
     * @throws \InvalidArgumentException If the base path does not exist or is not writable
     *
     * @return \M1\Vars\Vars
     */
    public function setBasePath($base_path)
    {
        if (!is_dir($base_path)) {
            throw new \InvalidArgumentException(sprintf(
                "'%s' base path does not exist or is not writable",
                $base_path
            ));
        }

        $this->base_path = realpath($base_path);
        return $this;
    }

    /**
     * Returns if cache is on or off
     *
     * @return bool Is cache on or off
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * Returns if the cache has been attempted
     *
     * @return bool Has the cache been attempted
     */
    public function getCacheAttempted()
    {
        return $this->cache_attempted;
    }

    /**
     * Returns the cache path
     *
     * @return string The cache path
     */
    public function getCachePath()
    {
        return $this->cache_path;
    }

    /**
     * Returns how long the cache lasts for
     *
     * @return int Cache expire time
     */
    public function getCacheExpire()
    {
        return $this->cache_expire;
    }

    /**
     * Returns when the cache was made
     *
     * @return int Cache creation time
     */
    public function getCacheTime()
    {
        return $this->cache_time;
    }

    /**
     * Sets the cache path
     *
     * @param string $cache_path The cache path to set
     *
     * @throws \InvalidArgumentException If the cache path does not exist or is not writable
     *
     * @return \M1\Vars\Vars
     */
    public function setCachePath($cache_path)
    {
        if (!is_dir($cache_path) || !is_writable($cache_path)) {
            throw new \InvalidArgumentException(sprintf(
                "'%s' cache path does not exist or is not writable",
                $cache_path
            ));
        }

        $this->cache_path = realpath($cache_path);
        return $this;
    }

    /**
     * Adds a resource to $this->resources
     *
     * @param string $resource Resource to add to the stack
     *
     * @return int The position of the added resource
     */
    public function addResource($resource)
    {
        $r = realpath($resource);
        $pos = count($this->resources);
        $this->resources[$pos] = $r;
        return $pos;
    }

    /**
     * Updates the string resource with the FileResource
     *
     * @param \M1\Vars\Resource\FileResource $resource The FileResource to add
     * @param int                            $pos      The position of the string resource
     *
     * @return \M1\Vars\Vars
     */
    public function updateResource($resource, $pos)
    {
        $this->resources[$pos] = $resource;
        return $this;
    }

    /**
     * Tests to see if the resource has been imported already -- this is to avoid getting into a infinite loop
     *
     * @param \M1\Vars\Resource\FileResource|string $resource Resource to check
     *
     * @return bool Has resource already been imported
     */
    public function resourceImported($resource)
    {
        $resource = realpath($resource);
        foreach ($this->getResources() as $r) {
            if ((is_a($r, 'M1\Vars\Resource\FileResource') && $resource === $r->getFile()) ||
                (is_string($r) && $resource === $r)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Searches the resource stack for a certain resource
     *
     * @param string $resource The resource to search for
     *
     * @return bool Returns the resource if found
     */
    public function getResource($resource)
    {
        foreach ($this->getResources() as $r) {
            if ($resource === $r->getFilename()) {
                return $r;
            }
        }

        return false;
    }

    /**
     * Returns the imported resources
     *
     * @return array The Vars imported resources
     */
    public function getResources()
    {
        return $this->resources;
    }

    /**
     * Returns the Vars replacement variables
     *
     * @return array The Vars replacement variables
     */
    public function getVariables()
    {
        return $this->variables;
    }
}
