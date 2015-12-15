# Vars

[![Author](http://img.shields.io/badge/author-@milescroxford-blue.svg?style=flat-square)](https://twitter.com/milescroxford)
[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]

<!---
[![Total Downloads][ico-downloads]][link-downloads]
--->

Vars is a simple to use and easily extendable configuration loader with built-in loaders for ENV, INI, JSON, PHP, Toml, XML and YAML file types. It also comes built-in support for Silex and more frameworks (Symfony, Laravel etc) to come soon.

## Requirements

Vars requires PHP version `5.3+`.

If you want to use YAML you'll need the [`symfony/yaml`](https://github.com/symfony/yaml) library and similarly you'll need [`yosymfony/toml`](https://github.com/yosymfony/toml) to use Toml files and [`m1/env`](https://github.com/m1/env) to use Env files.

## Install

Via Composer

``` bash
$ composer require m1/vars
```

## Usage
### Basic

``` php
// load single file
$vars = new Vars(__DIR__.'/config/config.yml');

// load from dir
$vars = new Vars(__DIR__.'/config');

// load from array
$vars = new Vars(array(
    __DIR__.'/config/config.yml',
    __DIR__.'/config/sub',
));
```

### Accessing the config

This can be done in various ways, you can treat the `$vars` variable as a normal array or you can use it in a object oriented manner

``` php
// All return the same thing
$vars->get('db.password')
$vars['db.password'];
$vars['db']['password']
```

You can also set values in the same manner

``` php
// All do the same thing
$vars->set('db.password', 'test')
$vars['db.password'] = 'test';
$vars['db']['password'] = 'test';
```

You can also get the variables from `getenv()`
``` php
// All do the same thing
$vars->toEnv();
getenv('db.password');
```
For more info on this check the [Environment Variables](#Environment-Variables) section

### Importing

#### Importing files
You can easily relatively and absolutely import configs into other configs, these differ by the config file type so check the /tests/mocks/ folder for examples

``` yml
# example_1.yml
test_key_1: test_value_1
imports: example_2.yml

# example_2.yml
test_key_2: test_value_2
```

Would return:
``` php
[
    "test_key_1" => "test_value_1",
    "test_key_2" => "test_value_2"
]
```

Imports are imported relative to the key by default, eg:

``` yml
test_key_1:
    imports: example_2.yml
```

Would return:
``` php
[
    "test_key_1" => [
        "test_key_2" => "test_value_2"
    ]
]
```

However you can change this various ways:
``` yml
# example 1
test_key_1:
    imports: 
    - {resource: example.yml, relative: false}

# example 2
test_key_2:
    imports:
        resource: example.yml
        relative: false
```

If importing various files and you want to set the relativity of all files you can do the following:
``` yml
test_key_1:
    imports: 
        relative: false
        resource:
            - example_2.yml
            - example_3.yml
```

All the above cause the `example_2.yml` and `example_3.yml` variables to become absolute to the config file:

``` php
[
    "test_key_1" => []
    "test_key_2" => "test_value_2" // from example_2.yml
    "test_key_3" => "test_value_3" // from example_3.yml
]
```

#### Importing directories

You can also import directories using all of the above syntax:
``` yml
test_key_1:
    imports: sub/
```

Importing directories is by default recursive and will search folders within folders, there will be a toggle to change
this in the near future.

The importing of directories relies on loaders and the extensions supported by the loaders. See the loader section for more detail.


### Resources

You can get individual files or resources:

``` php
// All return the same thing
$vars->getResource('example_2.yml')->get('test_key_2');
$vars->getResource('example_2.yml')['test_key_2'];
```

### Options

There are various options for Vars

``` php
$vars = new Vars(__DIR__.'/config/config.yml', [
    // this will affect how you getResource() and will  default to the path
    // of the first resource you initiate
    'base_path' => __DIR__.'/config',

    // to cache or not -- defaults to true
    'cache' => true,

    // where the cache is stored -- If not set will default to the base path
    'cache_path' => __DIR__./config/',

    // How long the cache lasts for -- Defaults to 300 seconds (5 minutes)
    'cache_expire' => 300,

    // Replacement variables -- see variables section for more detail
    'variables' => [
        'foo' => 'bar',
        'foobar' => 'barfoo'
    ],

    // The file loaders to load the configs -- see loader section for more detail
    'loaders' => [
        'default'
    ]
]);
```

#### Base path

The `base_path` is how the `$filename` in `$vars->getResource($filename)` is calculated. For example:

If you set the `base_path` to `__DIR__.'/config'` and you imported `__DIR__.'/app/test_1.yml'`:
``` yml
# example_1.yml
imports: example_2.yml
```

Then both the `example_1.yml` and `example_2.yml` `$filename` would be `../app/test_1.yml` and `../app/test_1.yml` respectively.

If no `base_path` is set then the first file resource path will be used as the `base_path`, eg:

``` php
// example 1
$vars = new Vars(__DIR__.'/config/config.yml');

// example 2
$vars = new Vars([
    __DIR__.'/config/config.yml',
    __DIR__.'/sub/config.yml',
    ]);
```

Will both use `__DIR__.'/config'` as the `base_path`

#### Variables

Variables enable you to do replacements in the resources, eg:
``` yml
test_key_1: %foo%
test_key_2: /bar/%foobar%/bar
```

``` php
$vars = new Vars(__DIR__.'/config/config.yml', [
    'variables' => [
        'foo' => 'bar',
        'foobar' => 'barfoo'
    ],
]);
```

Outputs:
``` php
[
    "test_key_1" => "bar",
    "test_key_2" => "/bar/foobar/"
]
```

Your replacements must be prefix and suffixed with `%`

You can also load variables from files:
``` php
$vars = new Vars(__DIR__.'/config/config.yml', [
    'variables' => __DIR__.'/config/variables.yml'
]);
```

#### Environment Variables

You can also use environment variables to do replacements:

``` yml
test_key_1: _ENV::DATABASE_USERNAME
test_key_2: _ENV::DATABASE_PASSWORD
```

``` nginx
# nginx config example
location @site {
    fastcgi_pass unix:/var/run/php5-fpm.sock;
    include fastcgi_params;
    fastcgi_param  SCRIPT_FILENAME $document_root/index.php;

    # env variables
    fastcgi_param DATABASE_USERNAME test_username;
    fastcgi_param DATABASE_PASSWORD test_password;
}
```

Outputs:
``` php
[
    "test_key_1" => "test_username",
    "test_key_2" => "test_password"
]
```

Your environment variables must be prefix with `_ENV::`

You can also make it so your config array is available to `getenv()`:

```php
$vars = new Vars(__DIR__.'/config/config.yml');
$vars->toEnv();
```

*Note:* Your config will be flattened to a dot notation for this, e.g.:

```yaml
test_key_1:
    test_key_2: value
```

Will be accessed by:
```php
getenv('test_key_1.test_key_2'); // value
```

#### Caching

Vars automatically caches the resources for 5 minutes, you can turn this off by setting the `cache` option to `false`.

The `cache_path` if not set is set to what the `base_path` is set to.

The cache file is a .php file due to the extra speedup of opcache.

#### Loaders

The loaders are what enable Vars to read the different file types (defaults are Ini, Json, Php, Toml, Xml and Yaml).

You can enable and disable loaders via the options:

Default loads all the default loaders:
``` php
$vars = new Vars(__DIR__.'/config/config.yml', [
    'loaders' => 'default'
]);

// You can load individual loaders:
$vars = new Vars(__DIR__.'/config/config.yml', [
    'loaders' => [
        'ini',
        'json'
    [
]);

//You can also create and load custom loaders:
$vars = new Vars(__DIR__.'/config/config.yml', [
    'loaders' => [
        '\Foo\Bar\CustomFooBarLoader',
        'ini',
        'json'
    ]
]);
```
To create your own custom loader you must extend `M1\Vars\Loader\AbstractLoader`, have the supported extensions in the
`public static $supported` array and have a `public function load()` that loads the content of the file.

Here is a primitive example that loads .txt files:

```php
namespace M1\Foo\Bar\Loader;

use M1\Vars\Loader\AbstractLoader;

class TextLoader extends AbstractLoader
{
    public static $supported = array('txt');

    public function load()
    {

        $content = [];

        foreach (file($this->entity) as $line) {
            list($key, $value) = explode(':', $line, 2);
            $content[trim($key)] = trim($value);
        }

        $this->content = $content;

        return $this;
    }
}
```

Then to use this loader, you would simply use:
```php
$vars = new Vars(__DIR__.'/config/config.yml', [
    'loaders' => [
        '\M1\Foo\Bar\Loader\TextLoader',
    ]
]);
```

*Note: don't use this loader for real, it is purely for presentational purposes*

### Providers

#### Silex

It's pretty straightforward to use this library with Silex, just register it when you register other service providers:
```php
$app->register(new M1\Vars\Provider\Silex\VarsServiceProvider(__DIR__.'/../../app/config/example.yml'), [
    'vars.path' => __DIR__.'/../../app/config/test/',
    'vars.options' => [
        'cache' => true,
        'cache_path' => __DIR__.'/../../app/config/cache/',
        'cache_expire' => 500,
        'variables' => [
            'test' => 'test_replacement'
        ],
        'loaders' => [
            'yml',
            'json'
        ],
        'variables' => __DIR__.'/../../app/config/replacements.json',
    ]]);
```

Then you can access your config from `$app['vars']`

*Note: If you `$app['debug'] = true` then the cache will not be used.*

## Todo

- Add toggle for recursive searching when importing directories, see issue #
- Add more providers (Symfony, Laravel, etc)

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email hello@milescroxford.com instead of using the issue tracker.

## Credits

- [m1](http://github.com/m1)
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/m1/Vars.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/m1/Vars/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/m1/Vars.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/m1/Vars.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/m1/Vars.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/m1/Vars
[link-travis]: https://travis-ci.org/m1/Vars
[link-scrutinizer]: https://scrutinizer-ci.com/g/m1/Vars/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/m1/Vars
[link-downloads]: https://packagist.org/packages/m1/Vars
[link-author]: https://github.com/m1
[link-contributors]: ../../contributors
