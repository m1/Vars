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

namespace M1\Vars\Cache;

use M1\Vars\Traits\PathTrait;
use M1\Vars\Vars;

/**
 * The CacheProvider provides the cached file if one exists and is requested
 *
 * @since 0.1.0
 */
class CacheProvider
{
    /**
     * Used for path functions and variables
     */
    use PathTrait;

    /**
     * Has the cache been attempted
     *
     * @var bool $attempted
     */
    private $attempted = false;

    /**
     * How long for the cache to be fresh
     *
     * @var int $expire
     */
    private $expire;

    /**
     * Has the cache been found
     *
     * @var bool $hit
     */
    private $hit = false;

    /**
     * The loaded Vars object from cache
     *
     * @var \M1\Vars\Vars $loaded_vars
     */
    private $loaded_vars;

    /**
     * The cache file name
     *
     * @var string $name
     */
    private $name;

    /**
     * Is the cache turned on
     *
     * @var boolean $provide
     */
    private $provide;

    /**
     * If cached, the time when the class was cached
     *
     * @var int $time
     */
    private $time;

    /**
     * Creates a new instance of the cacheProvider for Vars
     *
     * @param string|array $resource The main configuration resource
     * @param array        $options  The options being used for Vars
     */
    public function __construct($resource, $options)
    {
        $this->setProvide($options['cache']);
        $this->setPath($options['cache_path'], true);

        $this->expire = $options['cache_expire'];
        $this->name = md5(serialize($resource));
    }

    /**
     * Checks the cache to see if there is a valid cache available
     *
     * @return bool Returns true if has the cached resource
     */
    public function checkCache()
    {
        if ($this->provide && $this->path && !$this->getAttempted()) {
            $file = sprintf('%s/vars/%s.php', $this->path, $this->name);
            $this->attempted = true;

            if (is_file($file) &&
                filemtime($file) >= (time() - $this->expire)) {
                $this->hit = true;
                return true;
            }
        }
        return false;
    }

    /**
     * Load the cached file into $this->loaded_vars
     */
    public function load()
    {
        $cached_file = sprintf('%s/vars/%s.php', $this->path, $this->name);
        $this->loaded_vars = unserialize(file_get_contents($cached_file));
    }

    /**
     * Transfer the contents of the parent Vars object into a file for cache
     *
     * @param \M1\Vars\Vars $vars Parent vars object
     *
     * @codeCoverageIgnore
     */
    public function makeCache(Vars $vars)
    {
        if ($this->provide) {
            $cache_folder = sprintf("%s/vars", $this->path);
            if (!file_exists($cache_folder)) {
                mkdir($cache_folder, 0777, true);
            }

            $cache_file = sprintf('%s/%s.php', $cache_folder, $this->name);
            file_put_contents($cache_file, serialize($vars));
        }
    }

    /**
     * Returns if cache is on or off
     *
     * @return bool Is cache on or off
     */
    public function getProvide()
    {
        return $this->provide;
    }

    /**
     * Set the cache on or off
     *
     * @param bool $provide Does the cache want to be on or off
     *
     * @return \M1\Vars\Cache\CacheProvider
     */
    public function setProvide($provide)
    {
        $this->provide = $provide;
        return $this;
    }

    /**
     * Returns if the cache has been attempted
     *
     * @return bool Has the cache been attempted
     */
    public function getAttempted()
    {
        return $this->attempted;
    }

    /**
     * Returns how long the cache lasts for
     *
     * @return int Cache expire time
     */
    public function getExpire()
    {
        return $this->expire;
    }

    /**
     * Returns how long the cache lasts for
     *
     * @return int Cache expire time
     */
    public function isHit()
    {
        return $this->hit;
    }

    /**
     * Returns when the cache was made
     *
     * @return int Cache creation time
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Sets the time when the Vars was cached
     *
     * @param int $time Time when vars cached was created
     *
     * @return \M1\Vars\Cache\CacheProvider The cacheProvider object
     */
    public function setTime($time)
    {
        $this->time = $time;
        return $this;
    }

    /**
     * Returns the loaded Vars object
     *
     * @return \M1\Vars\Vars The loaded Vars object
     */
    public function getLoadedVars()
    {
        return $this->loaded_vars;
    }
}
