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

namespace M1\Vars\Variables;

use \M1\Vars\Resource\AbstractResource;

/**
 * Stores the in-file variables
 *
 * @since 1.0.0
 */
class VariableStore extends AbstractResource
{
    /**
     * The current prefix for the variable store
     *
     * @var string $current_prefix
     */
    private $current_prefix;

    /**
     * The relative prefix for the variable store
     *
     * @var string $path
     */
    private $prefix;

    /**
     * Creates a relative prefix for the store
     *
     * @param bool $relative Is the prefix relative
     */
    public function createPrefix($relative)
    {
        if ($relative) {
            $this->prefix = (empty($this->current_prefix)) ? '' : $this->current_prefix;
            return;
        }

        $this->prefix = '';
    }

    /**
     * Creates a new prefix name for the store
     *
     * @param string $prefix The prefix for the store
     * @param string $key The key for the item
     *
     * @return string The new prefix
     */
    public function createPrefixName($prefix, $key)
    {
        return $prefix.$key.'.';
    }

    /**
     * Get the prefix
     *
     * @return string The prefix
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Sets the current prefix
     *
     * @param string $prefix The new prefix
     */
    public function setCurrentPrefix($prefix)
    {
        $this->current_prefix = $prefix;
    }
}
