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
 * @version     0.3.0
 * @author      Miles Croxford <hello@milescroxford.com>
 * @copyright   Copyright (c) Miles Croxford <hello@milescroxford.com>
 * @license     http://github.com/m1/vars/blob/master/LICENSE
 * @link        http://github.com/m1/vars/blob/master/README.MD Documentation
 */

namespace M1\Vars\Provider\Silex;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use M1\Vars\Vars;

/**
 * The Silex service provider for Vars
 *
 * @since 0.1.0
 */
class VarsServiceProvider implements ServiceProviderInterface
{
    /**
     * The entity to use Vars with
     *
     * @var null
     */
    private $entity;

    /**
     * The available option keys
     *
     * @var array
     */
    private $option_keys = array(
        'cache',
        'cache_path',
        'cache_expire',
        'loaders',
        'variables'
    );

    /**
     * The service provider constructor sets the entity to use with vars
     *
     * @param mixed $entity The entity
     */
    public function __construct($entity = null)
    {
        $this->entity = $entity;
    }

    /**
     * Registers the service provider, sets the user defined options and returns the vars instance
     *
     * @param \Pimple\Container $container The pimple container
     */
    public function register(Container $container)
    {
        $container['vars'] = function ($container) {
            return new Vars($this->entity, $this->createOptions($container));
        };
    }

    /**
     * Creates the defined options into a way that Vars can use
     *
     * @param \Pimple\Container $container The pimple container
     *
     * @return array The created options
     */
    private function createOptions($container)
    {
        $options = array();

        if (isset($container['vars.path'])) {
            $options['path'] = $container['vars.path'];
        }

        if (isset($container['vars.options'])) {
            $options = $this->createKeyedOptions($options, $container['vars.options']);

        }

        if (isset($container['debug']) && $container['debug']) {
            $options['cache'] = false;
        }

        return $options;
    }

    /**
     * Registers the service provider, sets the user defined options and returns the vars instance
     *
     * @param array $options      The already parsed options
     * @param array $vars_options The options defined in the Silex app
     *
     * @return array The keyed options
     */
    private function createKeyedOptions($options, $vars_options)
    {
        foreach ($this->option_keys as $option) {
            $options[$option] = (isset($vars_options[$option])) ? $vars_options[$option] : null;
        }

        return $options;
    }
}
