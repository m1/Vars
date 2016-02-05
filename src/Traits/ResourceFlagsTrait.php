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
 * This trait manages the resource flags that you can use
 *
 * @since 0.3.0
 */
trait ResourceFlagsTrait
{
    /**
     * Explodes the ?: flag into pieces
     *
     * @param string $resource The resource string
     *
     * @return array The parsed resources
     */
    protected function explodeResourceIfElse($resource)
    {
        $resource = explode("?:", $resource, 2);
        $resources = array();

        foreach ($resource as $r) {
            $resources[] = trim($r);
        }

        return $resources;
    }

    /**
     * Returns the array pieces of the exploded ?: into a string
     *
     * @param array $resource The resource array
     *
     * @return string The parsed string resource
     */
    protected function implodeResourceIfElse($resource)
    {
        if (count($resource) > 1) {
            return implode(" ?: ", $resource);
        }

        return $resource[0];
    }

    /**
     * Returns trimmed resource string
     *
     * @param string $resource The resource
     *
     * @return string The parsed string resource
     */
    protected function trimFlags($resource)
    {
        $resource = trim($resource, '@');
        $resource = trim($resource, '*');

        return $resource;
    }

    /**
     * Duplicates the flags from one string to another
     *
     * @param string $resource The resource to add the flags to
     * @param string $resource The resource to duplicate the flags from
     *
     * @return string The parsed string resource
     */
    protected function replicateFlags($resource, $original_resource)
    {
        return sprintf(
            '%s%s%s',
            ($this->checkSuppression($original_resource)) ? '@' : null,
            $resource,
            ($this->checkRecursive($original_resource)) ? '*' : null
        );
    }

    /**
     * Checks to see if the recursive flag is set
     *
     * @param string $resource The resource
     *
     * @return bool Is the recursive flag set
     */
    private function checkRecursive($resource)
    {
        return substr($resource, -1) === "*";
    }

    /**
     * Checks to see if the file not found exception is suppressed
     *
     * @param string $resource The resource
     *
     * @return bool Is the exception suppressed
     */
    private function checkSuppression($resource)
    {
        return substr($resource, 0, 1) === "@";
    }
}
