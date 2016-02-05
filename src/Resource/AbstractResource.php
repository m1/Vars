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

namespace M1\Vars\Resource;

/**
 * Abstract Resource enables normal and dot notation array access on resources
 *
 * @since 0.1.0
 */
abstract class AbstractResource implements \ArrayAccess
{
    /**
     * The resource content
     *
     * @var array
     */
    protected $content = array();

    /**
     * Sets the resource contents
     *
     * @param array $content
     *
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Returns the content of the resource
     *
     * @return array The content
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Normal `$example[$key]` access for the array
     *
     * @param mixed $key The key to get the value for
     *
     * @return array|bool|null The resource key value
     */
    public function offsetGet($key)
    {
        return $this->internalGet($this->content, $key);
    }

    /**
     * Object oriented get access for the array
     *
     * @param mixed $key The key to get the value for
     *
     * @return array|bool|null The resource key value
     */
    public function get($key)
    {
        return $this->internalGet($this->content, $key);
    }

    /**
     * The internal get function for getting values by their key
     *
     * @param array $array  The array to use -- will always be $this->content
     * @param mixed $key    The key to find the value for
     * @param bool  $exists Whether to return null or false dependant on the calling function
     *
     * @return array|bool|null The resource key value
     */
    private function internalGet(array $array, $key, $exists = false)
    {
        if (isset($array[$key])) {
            return (!$exists) ? $array[$key] : true;
        }

        $parts = explode('.', $key);

        foreach ($parts as $part) {
            if (!is_array($array) || !isset($array[$part])) {
                return (!$exists) ? null : false;
            }

            $array = $array[$part];
        }

        return (!$exists) ? $array : true;
    }

    /**
     * Normal `$example[$key] = 'hello'` access for the array
     *
     * @param mixed $key The key to set the value for
     * @param mixed $value The value to set
     */
    public function offsetSet($key, $value)
    {
        $this->internalSet($this->content, $key, $value);
    }

    /**
     * Object oriented set access for the array
     *
     * @param string $key The key to set the value for
     * @param string $value The value to set
     */
    public function set($key, $value)
    {
        $this->internalSet($this->content, $key, $value);
    }

    /**
     * Object oriented set access for the array
     *
     * @param array $array  The array to use -- will always be based on $this->content but can be used recursively
     * @param mixed $key    The key to set the value for
     * @param mixed $value  The value to set
     *
     * @return array Returns the modified array
     */
    private function internalSet(array &$array, $key, $value)
    {
        if (is_null($key)) {
            return $array = $value;
        }

        $keys = explode('.', $key);

        while (count($keys) > 1) {
            $key = array_shift($keys);

            if (! isset($array[$key]) || ! is_array($array[$key])) {
                $array[$key] = [];
            }

            $array = &$array[$key];
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }

    /**
     * Checks whether the key exists
     *
     * @param mixed $key The key to check
     *
     * @return bool Does the key exist
     */
    public function offsetExists($key)
    {
        return $this->internalGet($this->content, $key, true);
    }

    /**
     * Unsets the key
     *
     * @param mixed $key The key to unset
     */
    public function offsetUnset($key)
    {
        $this->internalUnset($this->content, $key);
    }

    /**
     * Internal unset for the key
     *
     * @param array $array The array to use -- will always be based on $this->content but can be used recursively
     * @param mixed $key The key to unset
     */
    protected function internalUnset(array &$array, $key)
    {
        $parts = explode(".", $key);

        while (count($parts) > 1) {
            $part = array_shift($parts);

            if (isset($array[$part]) && is_array($array[$part])) {
                $array =& $array[$part];
            }
        }

        unset($array[array_shift($parts)]);
    }

    /**
     * Port of array_key_exists to \ArrayAccess
     *
     * @param mixed $key The key to check if exists
     *
     * @return bool Does the key exist
     */
    public function arrayKeyExists($key)
    {
        if (array_key_exists($key, $this->content)) {
            return true;
        }

        $parts = explode('.', $key);
        $arr = $this->content;
        foreach ($parts as $part) {
            if (!is_array($arr) || !array_key_exists($part, $arr)) {
                return false;
            }

            $arr = $arr[$part];
        }

        return true;
    }
}
