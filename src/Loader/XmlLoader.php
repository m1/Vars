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
 * The XML file loader
 *
 * @since 0.1.0
 */
class XmlLoader extends AbstractLoader
{
    /**
     * {@inheritdoc}
     */
    public static $supported = array('xml');

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException If the xml file fails to load
     */
    public function load()
    {
        libxml_use_internal_errors(true);
        $content = simplexml_load_file($this->entity);

        if (!$content) {
            throw new \RuntimeException(
                sprintf("'%s' failed to load with the error '%s'", $this->entity, libxml_get_errors()[0]->message)
            );
        }

        $this->content = json_decode(json_encode($content), true);
        return $this;
    }
}
