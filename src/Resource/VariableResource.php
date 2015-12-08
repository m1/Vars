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
 * @version     0.1.0
 * @author      Miles Croxford <hello@milescroxford.com>
 * @copyright   Copyright (c) Miles Croxford <hello@milescroxford.com>
 * @license     http://github.com/m1/vars/blob/master/LICENSE
 * @link        http://github.com/m1/vars/blob/master/README.MD Documentation
 */

namespace M1\Vars\Resource;

/**
 * Vars variable file resource for getting replacement variables from files or arrays
 *
 * @since 0.1.0
 */
class VariableResource
{
    /**
     * Basic file interaction logic
     */
    use FileTrait;

    /**
     * The replacement variables
     *
     * @var array $variables
     */
    private $variables;

    /**
     * Creates new instance of VariableResource
     *
     * @param \M1\Vars\Vars $vars Instance of the calling Vars
     * @param string|array  $data The file or array containing the variables
     */
    public function __construct($vars, $data)
    {
        $this->vars = $vars;

        $this->createContent($data);
    }

    /**
     * Creates the variable content from a file or array
     *
     * @throws \RuntimeException If variable data is not array or a file
     *
     * @param string|array  $data The file or array containing the variables
     */
    private function createContent($data)
    {
        if (is_array($data)) {
            $this->variables = $data;
        } elseif (is_file($data)) {
            $this->variables = $this->loadContent($data);
        } else {
            throw new \RuntimeException('Config variables must be a array or a file');
        }
    }

    /**
     * Returns the variable replacements
     *
     * @return array The variable replacements
     */
    public function getVariables()
    {
        return $this->variables;
    }
}
