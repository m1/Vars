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
 * The json file loader
 *
 * @since 0.1.0
 */
class JsonLoader extends AbstractLoader
{
    /**
     * {@inheritdoc}
     */
    public static $supported = array('json');

    /**
     * {@inheritdoc}
     *
     * @throws \RunntimeException If the json file fails to load
     */
    public function load()
    {
        $content = json_decode(file_get_contents($this->entity), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $message = function_exists('json_last_error_msg') ? json_last_error_msg() : 'Parse error';

            throw new \RuntimeException(
                sprintf("'%s' failed to load with the error '%s'", $this->entity, $message)
            );
        }

        $this->content = $content;
        return $this;
    }
}
