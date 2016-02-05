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
 * @copyright   Copyright (c) Miles Croxford <hello@milRescroxford.com>
 * @license     http://github.com/m1/vars/blob/master/LICENSE
 * @link        http://github.com/m1/vars/blob/master/README.MD Documentation
 */

namespace M1\Vars\Loader;

use Yosymfony\Toml\Toml;

/**
 * The Toml file loader
 *
 * @since 0.1.0
 */
class TomlLoader extends AbstractLoader
{
    /**
     * {@inheritdoc}
     */
    public static $supported = array('toml', 'tml');

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException If the `yosymfonytoml` library is not installed or the toml file is not valid
     */
    public function load()
    {
        try {
            $this->content = Toml::parse($this->entity);
        } catch (\Exception $e) {
            throw new \RuntimeException(
                sprintf("'%s' failed to load with the error '%s'", $this->entity, $e)
            );
        }
        return $this;
    }
}
