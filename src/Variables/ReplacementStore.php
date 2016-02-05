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
use M1\Vars\Traits\FileTrait;

/**
 * Stores the replacement variables
 *
 * @since 1.0.0
 */
class ReplacementStore extends AbstractResource
{
    /**
     * Basic file interaction logic
     */
    use FileTrait;

    /**
     * Creates new instance of VariableResource
     *
     * @param \M1\Vars\Vars $vars Instance of the calling Vars
     */
    public function __construct($vars)
    {
        $this->vars = $vars;
    }

    /**
     * Creates the replacement variable content from a file or array
     *
     * @param array $replacements The replacements

     * @throws \RuntimeException If variable data is not array or a file
     *
     * @return  array The replacement variables
     */
    public function load($replacements)
    {
        if (is_array($replacements)) {
            $variables = $replacements;
        } elseif (is_file($replacements)) {
            $variables = $this->loadContent($replacements);
        } else {
            throw new \RuntimeException('Config replacements must be a array or a file');
        }

        $this->content = $variables;
    }
}
