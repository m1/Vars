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
 * Path trait gives common operation functions needed for Paths in Vars
 *
 * @since 0.1.0
 */
trait PathTrait
{
    /**
     * The base path for the Vars config and cache folders
     *
     * @var string $path
     */
    public $path;

    /**
     * Get the path
     *
     * @return string The path
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set the Vars base path
     *
     * @param string  $path            The  path to set
     * @param boolean $check_writeable Check whether dir is writeable

     * @throws \InvalidArgumentException If the path does not exist or is not writable
     *
     * @return \M1\Vars\Vars
     */
    public function setPath($path, $check_writeable = false)
    {
        if (is_null($path)) {
            return;
        }

        if (!is_dir($path) || ($check_writeable && !is_writable($path))) {
            throw new \InvalidArgumentException(sprintf(
                "'%s' base path does not exist or is not writable",
                $path
            ));
        }

        $this->path = realpath($path);
        return $this;
    }
}
