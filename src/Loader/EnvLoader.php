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

use M1\Env\Parser;

/**
 * The Env file loader
 *
 * @since 0.2.0
 */
class EnvLoader extends AbstractLoader
{
    /**
     * {@inheritdoc}
     */
    public static $supported = array('env');

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException If `m1/env` library is not installed or the file can not be parsed
     */
    public function load()
    {
        try {
            $this->content = Parser::parse(file_get_contents($this->entity));
        } catch (\Exception $e) {
            throw new \RuntimeException(sprintf(
                "%s threw an exception: %s",
                $this->entity,
                $e
            ));
        }
        return $this;
    }
}
