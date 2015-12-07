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

namespace M1\Vars\Loader;

/**
 * The abstract file loader for loaders to be based on
 *
 * @since 0.1.0
 */
abstract class AbstractLoader
{
    /**
     * The content from the file
     *
     * @var mixed
     */
    protected $content;

    /**
     * The passed file to be loaded
     *
     * @var string
     */
    protected $file;

    /**
     * The supported extensions
     *
     * @var array
     */
    public static $supported = array();

    /**
     * Construct the file loader with the passed file
     *
     * @param string $file The passed file
     */
    public function __construct($file)
    {
        $this->file = $file;
    }

    /**
     * Checks whether the loader supports the file extension
     *
     * @return bool Does the loader support the file
     */
    public function supports()
    {
        $extension = pathinfo($this->file, PATHINFO_EXTENSION);
        return in_array($extension, static::$supported);
    }

    /**
     * Returns the file content
     *
     * @return mixed The content
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Hello load
     *
     * @return mixed
     */
    abstract public function load();
}
