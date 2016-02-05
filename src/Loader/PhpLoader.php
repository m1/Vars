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
 * The PHP file loader
 *
 * @since 0.1.0
 */
class PhpLoader extends AbstractLoader
{
    /**
     * {@inheritdoc}
     */
    public static $supported = array('php', 'php5');

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException If the php file doesn't return an array
     */
    public function load()
    {
        $content = require $this->entity;

        if (is_callable($content)) {
            $content = call_user_func($content);
        }

        if (!is_array($content)) {
            throw new \RuntimeException(sprintf("'%s' does not return an array", $this->entity));
        }

        $this->content = $content;
        return $this;
    }
}
