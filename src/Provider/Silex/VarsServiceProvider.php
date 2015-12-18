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
     * @param \Silex\Application $app The silex app
     */
    public function register(Application $app)
    {
        $app['vars'] = function ($app) {
            $options = array();

            if (isset($app['vars.path'])) {
                $options['base_path'] = $app['vars.path'];
            }

            if (isset($app['vars.options'])) {
                $keyed_options = $app['vars.options'];

                foreach ($this->option_keys as $option) {
                    if (isset($keyed_options[$option])) {
                        $options[$option] = $keyed_options[$option];
                    }
                }
            }

            if (isset($app['debug']) && $app['debug']) {
                $options['cache'] = false;
            }

            return new Vars($this->entity, $options);
        };
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
