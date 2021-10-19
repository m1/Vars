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

use InvalidArgumentException;
use M1\Vars\Vars;

/**
 * Vars variable file resource for getting replacement variables from files or arrays
 *
 * @since 0.1.0
 */
class VariableProvider
{
    /**
     * The replacement regex to get content between %^content%
     *
     * @var string REGEX_ENV_SYNTAX
     */
    const REGEX_ENV_SYNTAX         = '\\%\\^\\s*?([A-Za-z0-9 _ .]+)\\s*?\\%';

    /**
     * The variable regex to get content between %content%
     *
     * @var string REGEX_REPLACEMENT_SYNTAX
     */
    const REGEX_REPLACEMENT_SYNTAX = '\\%\\s*?([A-Za-z0-9 _ .]+)\\s*?\\%';

    /**
     * The variable regex to get content between %$content%
     *
     * @var string REGEX_VARIABLE_SYNTAX
     */
    const REGEX_VARIABLE_SYNTAX    = '\\%\\$\\s*?([A-Za-z0-9 _ .]+)\\s*?\\%';


    /**
     * The types of variables
     *
     * @var array $variable_types
     */
    private static array $variable_types = array('replacement', 'variable', 'env');

    /**
     * The replacement store
     *
     * @var ReplacementStore $rstore
     */
    public ReplacementStore $rstore;

    /**
     * The variable store
     *
     * @var VariableStore $vstore
     */
    public VariableStore $vstore;

    /**
     * Creates new instance of VariableProvider
     *
     * @param Vars $vars Instance of the calling Vars
     */
    public function __construct(Vars $vars)
    {
        $this->vstore = new VariableStore();
        $this->rstore = new ReplacementStore($vars);
    }

    /**
     * Parses the string for the types of variables
     *
     * @param string $value The string to parse
     *
     * @return string The parsed variable
     */
    public function parse(string $value): string
    {
        foreach (self::$variable_types as $variable_type) {
            $value = $this->typeParse($value, $variable_type);
        }

        return $value;
    }

    /**
     * Parses the string based on the type of variable
     *
     * @param string $value The string to parse
     * @param string $type  The variable type
     *
     * @return string The parsed variable
     */
    private function typeParse(string $value, string $type): string
    {
        $const_str = sprintf('REGEX_%s_SYNTAX', strtoupper($type));
        $regex = constant('\M1\Vars\Variables\VariableProvider::'.$const_str);

        $matches = $this->fetchVariableMatches($value, $regex);

        if (is_array($matches)) {
            if (count($matches[0]) === 1 && $value === $matches[0][0]) {
                return $this->fetchVariable(trim($matches[1][0]), $type);
            }

            $value = $this->doReplacements($value, $matches, $type);
        }
        return $value;
    }

    /**
     * Fetches the variable matches in the string
     *
     * @param string $value The string to fetch matches for
     * @param string $regex The variable type regex
     *
     * @return array|false
     */
    private function fetchVariableMatches(string $value, string $regex)
    {
        preg_match_all('/' . $regex . '/', $value, $matches);

        if (!is_array($matches) || !isset($matches[0]) || empty($matches[0])) {
            return false;
        }

        return $matches;
    }

    /**
     * Fetches the variable from the stores
     *
     * @param string $variable_name The variable to fetch
     * @param string $type          The variable type
     *
     * @return string The fetches value for the variable
     */
    private function fetchVariable(string $variable_name, string $type): string
    {
        $this->checkVariableExists($variable_name, $type);

        if ($type === 'env') {
            return getenv($variable_name);
        } elseif ($type === 'replacement') {
            return $this->rstore->get($variable_name);
        }

        return $this->vstore->get($variable_name);
    }

    /**
     * Checks to see if the variable exists
     *
     * @param string $variable The variable to check
     * @param string $type     The variable type
     *
     * @return void Does the variable exist
     *@throws InvalidArgumentException If the variable does not exist
     *
     */
    private function checkVariableExists(string $variable, string $type): void
    {
        if (($type === 'env'         && !getenv($variable)) ||
            ($type === 'replacement' && !$this->rstore->arrayKeyExists($variable)) ||
            ($type === 'variable'    && !$this->vstore->arrayKeyExists($variable))
        ) {
            throw new InvalidArgumentException(
                sprintf('Variable has not been defined as a `%s`: %s', $variable, $type)
            );
        }

    }

    /**
     * Does the replacements in the string for the variable
     *
     * @param string $value   The string to parse
     * @param array $matches The matches
     * @param string $type    The variable type
     *
     * @return string The parsed variable
     */
    public function doReplacements(string $value, array $matches, string $type): string
    {
        $replacements = array();
        for ($i = 0; $i <= (count($matches[0]) - 1); $i++) {
            $replacement = $this->fetchVariable($matches[1][$i], $type);
            $replacements[$matches[0][$i]] = $replacement;
        }

        if (!empty($replacements)) {
            $value = strtr($value, $replacements);
        }

        return $value;
    }
}
