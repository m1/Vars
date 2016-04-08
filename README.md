# Vars

[![Author](http://img.shields.io/badge/author-@milescroxford-blue.svg?style=flat-square)](https://twitter.com/milescroxford)
[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

Vars is a simple to use, lightweight and easily extendable configuration loader with built-in loaders for ENV, INI, JSON, PHP, Toml, XML and YAML file types. It also comes built-in support for Silex with more frameworks (Symfony, Laravel etc) to come soon.

* [Why?](#why)
* [Requirements](#requirements)
* [Install](#install)
* [Usage](#usage)
    * [Basic](#basic)
    * [Accessing the config](#accessing-the-config)
    * [Importing](#importing)
        * [Files](#importing-files)
        * [Directories](#importing-directories)
        * [Flag options](#flag-options)
    * [Resources](#resources)
    * [Options](#options)
        * [Path](#base-path)
        * [Variables](#variables)
            * [Replacement Variables](#replacements-variables)
            * [In-file Variables](#in-file-variables)
            * [Environment Variables](#environment-variables)
        * [Caching](#caching)
        * [Loaders](#loaders)
    * [Providers](#providers)
        * [Silex](#silex)
    * [Public API](#public-api)
        * [Vars](#vars-1)
            * [Constructor](#varsresource-options--array)
            * [getContent](#getcontent)
            * [getResource](#getresourceresource)
            * [getResources](#getresources)
            * [toEnv](#toenv)
            * [toDots](#todots)
            * [set](#setkey-value)
            * [get](#getkey)
        * [FileResource](#fileresource)
            * [getRawContent](#getrawcontent)
            * [getContent](#getcontent-1)
            * [get](#getkey-1)
* [Todo](#todo)
* [Change log](#change-log)
* [Testing](#testing)
* [Contributing](#Contributing)
* [Security](#security)
* [Credits](#credits)
* [License](#license)

## Why?

Sometimes you're forced to use different formats for config files and one of Vars aims is to make this simpler for you
by supporting the most common config formats so you don't have to switch libraries to deal with the different formats.

Another aim is to support different frameworks so again you don't have to switch libraries when dealing with different frameworks.
Currently only supporting Silex using a service provider, support for Laravel and Symfony to follow shortly.

With a simple API and intuitive loading options, Vars tries to make config loading and providing as easy as possible for you.

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

Importing directories is by default not recursive and will not search folders within folders, you can change this by adding a recursive toggle:
``` yml
test_key_1:
    imports:
        resource: sub/
        recursive: true
```

or by adding a recursive flag:
``` yml
test_key_1:
    imports:
        resource: sub/*
```

As with the loading files, you can bulk import dirs with one recursive toggle:
``` yml
test_key_1:
    imports:
        recursive: false
        resource:
            - sub/
            - sub1/
```

The importing of directories relies on loaders and the extensions supported by the loaders. See the loader section for more detail.

#### Flag options

You can use various flags when importing.

The if else flag `?:` makes it so if the first file exists, use that -- else use the other defined file, eg:

```yml
imports: "example_1.yml ?: example_2.yml"
```

*Note: You need to wrap the string in quotes for the if else flag to work*

The suppress exceptions flag `@` -- suppresses files not found exceptions. eg:

```yml
imports: @file_does_not_exist.yml
```

The recursive flag makes it so directories within directories are searched for files. eg:
``` yml
imports:
    resource: sub/*
```

You can also combine the above flags, so if the else file option does not exist, it won't throw an exception, eg:

```yml
imports: "example_1.yml ?: @example_2.yml"
```

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
    'path' => __DIR__.'/config',

    // to cache or not -- defaults to true
    'cache' => true,

    // where the cache is stored -- If not set will default to the base path
    'cache_path' => __DIR__.'/config/',

    // How long the cache lasts for -- Defaults to 300 seconds (5 minutes)
    'cache_expire' => 300,

    // Replacement variables -- see variables section for more detail
    'replacements' => [
        'foo' => 'bar',
        'foobar' => 'barfoo'
    ],
    
    // Merge globals -- see globals section for more detail
    'merge_globals' => true,

    // The file loaders to load the configs -- see loader section for more detail
    'loaders' => [
        'default'
    ]
]);
```

#### Base path

The `path` is how the `$filename` in `$vars->getResource($filename)` is calculated. For example:

If you set the `path` to `__DIR__.'/config'` and you imported `__DIR__.'/app/test_1.yml'`:
``` yml
# example_1.yml
imports: example_2.yml
```

Then both the `example_1.yml` and `example_2.yml` `$filename` would be `../app/test_1.yml` and `../app/test_1.yml` respectively.

If no `path` is set then the first file resource path will be used as the `path`, eg:

``` php
// example 1
$vars = new Vars(__DIR__.'/config/config.yml');

// example 2
$vars = new Vars([
    __DIR__.'/config/config.yml',
    __DIR__.'/sub/config.yml',
    ]);
```

Will both use `__DIR__.'/config'` as the `path`

#### Variables

You can use 3 types of variables in `Vars`: `Replacements`, `In-file` and `Environment`, the syntax is:

| Variable Type  | Syntax |
| ------------- | ------------- |
| Replacements  | `%VARIABLE%`  |
| In-file | `%$VARIABLE%`  |
| Environment | `%^VARIABLE%`  |

For better readability you can also put spaces between the variable name and the prefix/suffixes like so:
```yml
replacement_variable: % VARIABLE %
infile_variable: %$ VARIABLE %
env_variable: %^ VARIABLE %
```

##### Replacements

Replacement variables are loaded from outside `Vars`, so it's often used for `PHP` functions/logic, such as `__dir__`: 
``` yml
test_key_1: %foo%
test_key_2: /bar/%foobar%/bar
```

``` php
$vars = new Vars(__DIR__.'/config/config.yml', [
    'replacements' => [
        'foo' => 'bar',
        'foobar' => 'barfoo'
    ],
]);
```

Outputs:
``` php
[
    "test_key_1" => "bar",
    "test_key_2" => "/bar/barfoo/foobar/"
]
```

Your replacements must be prefix and suffixed with `%`

You can also load variables from files:
``` php
$vars = new Vars(__DIR__.'/config/config.yml', [
    'replacements' => __DIR__.'/config/variables.yml'
]);
```

##### In-file Variables

You can also use variables from your already defined keys in the files, such as:
``` yml
test_key_1: hello
test_key_2: /bar/%$test_key_1%/bar
```

Outputs:
``` php
[
    "test_key_1" => "bar",
    "test_key_2" => "/bar/hello/foobar/"
]
```

Your replacements must be prefix with `%$` and suffixed with `%`.

For both `in-file` and `replacements`, you can use the dot notation syntax to get in arrays, e.g:
``` yml
test_key_1: 
    test_key_2: hello
test_key_3: /bar/%$test_key_1.test_key_2%/bar
```

Outputs:
``` php
[
    "test_key_1" => array(
        "test_key_2" => "hello"
    ),
    "test_key_2" => "/bar/hello/foobar/"
]
```

##### Environment Variables

You can also use environment variables to do replacements:

``` yml
test_key_1: %^DATABASE_USERNAME%
test_key_2: %^DATABASE_PASSWORD%
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

Your environment variables must be prefix with `%^` and suffixed with `%`

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

#### Globals

`Globals` in `Vars` refer to variables defined as such:

```yaml
_globals:
    test_key_1: test_value_1
```

Basically they are just encapsulated in an `_globals` array -- the use of these are so you can access them from `getGlobals()` from `Vars`

The default action is to merge them into the other file contents, so that:

```yaml
_globals:
    test_key_1: test_value_1
test_key_2: test_value_2
```

Becomes:
```php
[
    'test_key_1' => 'test_value_1',
    'test_key_2' => 'test_value_2',
]
```
But you can override this by changing `merge_globals` to `false` via the options.

If this doesn't make sense then you probably won't need to use globals at all, but they're useful for working with framesworks
which encapsulate everything under say `$app` and you want to be able to access some key => values like so: `$app['test_key_1']`. 
See the Silex provider section for more examples.

#### Caching

Vars automatically caches the resources for 5 minutes, you can turn this off by setting the `cache` option to `false`.

The `cache_path` if not set is set to what the `path` is set to. The `cache_path` must be writeable.

To invalidate the cache, simply just remove the folder inside your `cache_path` called `vars`, eg: `rm -rf /var/www/application/app/cache/vars`

The cache file is a .php file due to the extra speedup of opcache.

If you're using the Silex provider, then the cache will not be used and set if you're in debug mode.

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
$app->register(new M1\Vars\Provider\Silex\VarsServiceProvider('example.yml'), [
    'vars.path' => __DIR__.'/../../app/config/test/',
    'vars.options' => [
        'cache' => true,
        'cache_path' => __DIR__.'/../../app/config/cache/',
        'cache_expire' => 500,
        'replacements' => [
            'test' => 'test_replacement'
        ],
        'loaders' => [
            'yml',
            'json'
        ],
        'merge_globals' => true,
        'replacements' => __DIR__.'/../../app/config/replacements.json',
    ]]);
```

Then you can access your config from `$app['vars']`

*Note: If you `$app['debug'] = true` then the cache will not be used.*

You can also access the config values from $app by using the dot notation, e.g:
```yaml
test_key_1:
    test_key_2: value
test_key_3: value
```

You can get the above using the dot notation like so:
```php
$app['vars']['test_key_1.test_key_2']; // value
$app['vars']['test_key_3']; // value
```

You can also merge globals into `$app` like so:

```yaml
# example.yml
_globals:
    monolog.logfile: log.log
test_key_1: test_value_2
```

```php
$app->register(new M1\Vars\Provider\Silex\VarsServiceProvider('example.yml'));

// register monolog here and other service providers

$app['vars.merge']();
```

Note the `$app['vars.merge']()` -- This overrides the service provider defaults so in this example `monolog` will use 
the log file defined in the vars config.

You must call `vars.merge` after you've called the service providers you provide config values for in your config.

You can also access `test_key_1` via `$app['vars.test_key_1']` and similary if you want, you can access globals like so
`$app['monolog.logfile']`.

## Public API

### Vars

##### `Vars($resource, $options = array())`

The constructor to create a new Vars config:

``` php
$vars = new Vars(__DIR__.'/config/config.yml', [
    // this will affect how you getResource() and will  default to the path
    // of the first resource you initiate
    'path' => __DIR__.'/config',

    // to cache or not -- defaults to true
    'cache' => true,

    // where the cache is stored -- If not set will default to the base path
    'cache_path' => __DIR__.'/config/',

    // How long the cache lasts for -- Defaults to 300 seconds (5 minutes)
    'cache_expire' => 300,

    // Replacement variables -- see variables section for more detail
    'replacements' => [
        'foo' => 'bar',
        'foobar' => 'barfoo'
    ],

    // The file loaders to load the configs -- see loader section for more detail
    'loaders' => [
        'default'
    ]
]);
```

##### `getContent()`

Returns the parsed content of all the configs.


##### `getResource($resource)`

Get a specified resource, returns a file resource or false if resource doesn't exist.

The `$resource` name is based on the path defined in base path and the filename.

```yml
# example.yml
imports: example2.yml
test_1: value

# example2.yml
test_2: value
```

```php
$vars = new Vars('example.yml');
$vars->getResource('example2.yml'); // FileResource

$vars->getResource('example2.yml')->getContent();
# output:
# [
#     "test_2" => "value"
# ]
```


##### `getResources()`

Returns all the resources imported, they will be `FileResource` objects.


##### `toEnv()`

Makes it so the config is available via `getenv()`:

```php
$vars = new Vars('example.yml');
$vars->toEnv();

getenv('test_1'); // value

```


##### `toDots()`

Makes it so the config is flattened into a dot notation array

```yml
test_value_1:
    test_value_2: value
    test_value_3: value
```

```php
$vars = new Vars('example.yml');
$vars->toDots();
# output:
# [
#     "test_value_1.test_value_2" => "value",
#     "test_value_1.test_value_3" => "value
# ]
```

##### `getGlobals()`

Gets the values defined in `_globals`

##### `set($key, $value)`
Set a config key:

```php
$vars = new Vars('example.yml');
$vars->set('test_key_1', 'value_2');
```


##### `get($key)`
Gets a config key:

```php
$vars = new Vars('example.yml');
$vars->get('test_key_1'); // value
```


### FileResource


##### `getRawContent()`

Get the raw, unparsed content from the file

```yml
# example.yml
test_value_1:
    imports: example2.yml
test_value_2: %root%/foo/%dir%
```

```php
$vars = new Vars('example.yml');
$vars->getResource('example.yml')->getRawContent();
# output:
# [
#     test_value_1:
#          imports: example2.yml
#     test_value_2: %root%/foo/%dir%
# ]
```


##### `getContent()`
See [getContent()](#getcontent) 


##### `get($key)`
See [get()](#getkey)


## Todo

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
