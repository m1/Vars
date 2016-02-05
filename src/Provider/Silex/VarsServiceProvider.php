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

namespace M1\Vars\Provider\Silex;

use Silex\Application;
use Silex\ServiceProviderInterface;
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
        'replacements',
        'merge_globals',
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
     * @param \Silex\Application $app The silex app
     */
    public function register(Application $app)
    {
        $app['vars'] = function ($app) {
            return new Vars($this->entity, $this->createOptions($app));
        };

        $app['vars.merge'] = $app->protect(function () use ($app) {
            static $initialized = false;
            if ($initialized) {
                return;
            }
            $initialized = true;

            foreach ($app['vars']->getGlobals() as $key => $value) {
                $app[$key] = $value;
            }

            foreach ($app['vars']->toDots(false) as $key => $value) {
                $app['vars.'.$key] = $value;
            }
        });
    }

    /**
     * Creates the defined options into a way that Vars can use
     *
     * @param \Silex\Application $app The silex app
     *
     * @return array The created options
     */
    private function createOptions($app)
    {
        $options = array();

        if (isset($app['vars.path'])) {
            $options['path'] = $app['vars.path'];
        }

        if (isset($app['vars.options'])) {
            $options = $this->createKeyedOptions($options, $app['vars.options']);
        }

        if (!isset($options['merge_globals']) || is_null($options['merge_globals'])) {
            $options['merge_globals'] = false;
        }

        if (isset($app['debug']) && $app['debug']) {
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

    /**
     * The silex service provider boot function
     *
     * @param \Silex\Application $app The silex app
     *
     * @codeCoverageIgnore
     */
    public function boot(Application $app)
    {
    }
}
