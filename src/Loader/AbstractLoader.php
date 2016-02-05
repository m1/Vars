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
 * The abstract loader for loaders to be based on
 *
 * @since 0.1.0
 */
abstract class AbstractLoader
{
    /**
     * The content from the entity
     *
     * @var mixed
     */
    protected $content;

    /**
     * The passed entity to be loaded
     *
     * @var string
     */
    protected $entity;

    /**
     * The supported extensions
     *
     * @var array
     */
    public static $supported = array();

    /**
     * Construct the loader with the passed entity
     *
     * @param string $entity The passed entity
     */
    public function __construct($entity)
    {
        $this->entity = $entity;
    }

    /**
     * The function what loads the content from the entity
     *
     * @return mixed
     */
    abstract public function load();

    /**
     * Checks whether the loader supports the file extension
     *
     * @return bool Does the loader support the file
     */
    public function supports()
    {
        $extension = pathinfo($this->entity, PATHINFO_EXTENSION);
        return in_array($extension, static::$supported);
    }

    /**
     * Sets what the loader supports
     *
     * @param array $supports Set the extensions the loader supports
     */
    public function setSupports(array $supports)
    {
        static::$supported = $supports;
    }

    /**
     * Returns the content
     *
     * @return mixed The content
     */
    public function getContent()
    {
        return $this->content;
    }
}
