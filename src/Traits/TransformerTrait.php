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
 * @version     0.2.0
 * @author      Miles Croxford <hello@milescroxford.com>
 * @copyright   Copyright (c) Miles Croxford <hello@milescroxford.com>
 * @license     http://github.com/m1/vars/blob/master/LICENSE
 * @link        http://github.com/m1/vars/blob/master/README.MD Documentation
 */

namespace M1\Vars\Traits;

/**
 * Vars transformer class for to*() functions
 *
 * @since 0.2.0
 */
trait TransformerTrait
{

    /**
     * Makes it so the content is available in getenv()
     */
    public function toEnv(){
        $dots = $this->toDots($this->content);

        foreach ($dots as $dot_k => $dot_v) {
            putenv(sprintf('%s=%s', $dot_k, $dot_v));

        }
    }

    /**
     * Converts the array into a flat dot notation array
     *
     * @return array The dot notation array
     */
    public function toDots()
    {
        return $this->dotTransformer($this->content);
    }

    /**
     * Converts the array into a flat dot notation array
     *
     * @param array  $content The content array
     * @param string $prefix  The prefix for the key
     *
     * @return array The dot notation array
     */
    private function dotTransformer($content, $prefix = '')
    {
        $parsed = array();

        foreach ($content as $arr_k => $arr_v) {
            if (is_array($arr_v)) {
                $parsed = array_merge($parsed, $this->dotTransformer($arr_v, $prefix.$arr_k."."));
            } else {
                $parsed[$prefix.$arr_k] = $arr_v;
            }
        }

        return $parsed;
    }

}