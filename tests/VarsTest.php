<?php

namespace M1\Vars\Test;

use InvalidArgumentException;
use M1\Vars\Provider\Silex\VarsServiceProvider;
use M1\Vars\Test\Plugin\FakeFileResource;
use M1\Vars\Vars;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Silex\Application;
use stdClass;

class VarsTest extends TestCase
{
    /**
     * @var string[]
     */
    private array $default_loaders_namespace;
    /**
     * @var string[]
     */
    private array $default_loaders;
    /**
     * @var string[]
     */
    private array $basic_array;

    public function setUp()
    {
        $this->basic_array = array(
            'test_key_1' => 'test_value_1',
            'test_key_2' => 'test_value_2',
        );

        $this->default_loaders = array('ini', 'json', 'php', 'toml', 'yaml', 'xml',);
        $this->default_loaders_namespace = array(
            'M1\Vars\Loader\EnvLoader',
            'M1\Vars\Loader\IniLoader',
            'M1\Vars\Loader\JsonLoader',
            'M1\Vars\Loader\PhpLoader',
            'M1\Vars\Loader\TomlLoader',
            'M1\Vars\Loader\YamlLoader',
            'M1\Vars\Loader\XmlLoader',
        );
    }

    public function testBasicValidYML()
    {
        $vars = new Vars(
            __DIR__ . '/mocks/basic/test_pass_1.yml',
            array(
                'cache' => false,
            )
        );

        $this->assertEquals($this->basic_array, $vars->getContent());
    }

    public function testBasicValidJson()
    {
        $vars = new Vars(
            __DIR__ . '/mocks/basic/test_pass_1.json',
            array(
                'cache' => false,
            )
        );

        $this->assertEquals($this->basic_array, $vars->getContent());
    }

    public function testBasicValidPHP()
    {
        $vars = new Vars(
            __DIR__ . '/mocks/basic/test_pass_1.php',
            array(
                'cache' => false,
            )
        );

        $this->assertEquals($this->basic_array, $vars->getContent());
    }

    public function testBasicValidCallablePHP()
    {
        $vars = new Vars(
            __DIR__ . '/mocks/basic/test_pass_3.php',
            array(
                'cache' => false,
            )
        );

        $this->assertEquals($this->basic_array, $vars->getContent());
    }

    public function testBasicValidXML()
    {
        $vars = new Vars(
            __DIR__ . '/mocks/basic/test_pass_1.xml',
            array(
                'cache' => false,
            )
        );

        $this->assertEquals($this->basic_array, $vars->getContent());
    }

    public function testBasicValidToml()
    {
        $vars = new Vars(
            __DIR__ . '/mocks/basic/test_pass_1.toml',
            array(
                'cache' => false,
            )
        );

        $this->assertEquals($this->basic_array, $vars->getContent());
    }

    public function testBasicValidIni()
    {
        $vars = new Vars(
            __DIR__ . '/mocks/basic/test_pass_1.ini',
            array(
                'cache' => false,
            )
        );

        $this->assertEquals($this->basic_array, $vars->getContent());
    }

    public function testBasicValidEnv()
    {
        $vars = new Vars(
            __DIR__ . '/mocks/basic/test_pass_1.env',
            array(
                'cache' => false,
            )
        );

        $this->assertEquals($this->basic_array, $vars->getContent());
    }

    public function testBasicDir()
    {
        $vars = new Vars(
            __DIR__ . '/mocks/dir/',
            array(
                'cache' => false,
            )
        );

        $this->assertEquals($this->basic_array, $vars->getContent());
    }

    public function testBasicArray()
    {
        $vars = new Vars(
            array(
                __DIR__ . '/mocks/basic/test_pass_1.php',
                __DIR__ . '/mocks/basic/test_pass_1.yml',
            ),
            array(
                'cache' => false,
            )
        );

        $this->assertEquals($this->basic_array, $vars->getContent());
    }

    public function testBasicImporting()
    {
        $expected = array_merge($this->basic_array, array(
            'test_key_3' => array(
                'test_key_4' => 'test_value_4',
            ),
            'test_key_5' => 'test_value_5',
        ));

        $vars = new Vars(
            __DIR__ . '/mocks/importing/basic_1.yml',
            array(
                'cache' => false,
            )
        );

        $this->assertEquals($expected, $vars->getContent());
    }

    public function testLoadSameFile()
    {
        $vars = new Vars(
            __DIR__ . '/mocks/importing/same_1.yml',
            array(
                'cache' => false,
            )
        );
        $this->assertTrue(count($vars->getResources()) === 1);
    }

    public function testRelativeResourceArrayImporting()
    {
        $expected = array(
            'test_key_1' => array(
                'test_key_2' => 'test_value_2',
                'test_key_3' => 'test_value_3',
            ),
            'test_key_4' => 'test_value_4',
        );

        $vars = new Vars(
            __DIR__ . '/mocks/importing/relative_resource_array_1.yml',
            array(
                'cache' => false,
            )
        );

        $this->assertEquals($expected, $vars->getContent());
    }

    public function testRelativeAcceptedtringsImporting()
    {
        $expected = array(
            'test_key_1' => array(
                'test_key_2' => 'test_value_2',
                'test_key_3' => 'test_value_3',
                'test_key_4' => 'test_value_4',
            ),
            'test_key_5' => 'test_value_5',
            'test_key_6' => 'test_value_6',
        );

        $vars = new Vars(
            __DIR__ . '/mocks/importing/relative_string_1.yml',
            array(
                'cache' => false,
            )
        );

        $this->assertEquals($expected, $vars->getContent());
    }

    public function testImportMultiArrayImporting()
    {
        $expected = array(
            'test_key_1' => array(
                'test_key_2' => 'test_value_2',
            ),
            'test_key_3' => 'test_value_3',
        );

        $vars = new Vars(
            __DIR__ . '/mocks/importing/multi_array_1.yml',
            array(
                'cache' => false,
            )
        );

        $this->assertEquals($expected, $vars->getContent());
    }

    public function testImportArrayImporting()
    {
        $expected = array(
            'test_key_1' => array(
                'test_key_2' => 'test_value_2',
                'test_key_3' => 'test_value_3',
            ),
        );

        $vars = new Vars(
            __DIR__ . '/mocks/importing/basic_array_1.yml',
            array(
                'cache' => false,
            )
        );

        $this->assertEquals($expected, $vars->getContent());
    }

    public function testImportStringImporting()
    {
        $expected = array(
            'test_key_1' => array(
                'test_key_2' => 'test_value_2',
            ),
        );

        $vars = new Vars(
            __DIR__ . '/mocks/importing/basic_string_1.yml',
            array(
                'cache' => false,
            )
        );

        $this->assertEquals($expected, $vars->getContent());
    }

    public function testImportStringSuppressException()
    {
        $expected = array();

        $vars = new Vars(
            '@'.__DIR__ . '/mocks/NONE.yml',
            array(
                'cache' => false,
            )
        );

        $this->assertEquals($expected, $vars->getContent());
    }

    public function testImportStringIfElseBasic()
    {
        $expected = array('test_key_1' => 'test_value_1');

        $vars = new Vars(
            __DIR__ . '/mocks/importing/ifelse_1.yml',
            array(
                'cache' => false,
            )
        );

        $this->assertEquals($expected, $vars->getContent());
    }

    public function testImportStringIfElseElseResult()
    {
        $expected = array('test_key_2' => 'test_value_2');

        $vars = new Vars(
            __DIR__ . '/mocks/importing/ifelse_2.yml',
            array(
                'cache' => false,
            )
        );

        $this->assertEquals($expected, $vars->getContent());
    }

    public function testImportStringIfElseElseEmpty()
    {
        $expected = array('test_key_1' => 'test_value_1');

        $vars = new Vars(
            __DIR__ . '/mocks/importing/ifelse_3.yml',
            array(
                'cache' => false,
            )
        );

        $this->assertEquals($expected, $vars->getContent());
    }

    public function testImportStringIfElseSuppressElse()
    {
        $expected = array();

        $vars = new Vars(
            __DIR__ . '/mocks/importing/ifelse_4.yml',
            array(
                'cache' => false,
            )
        );

        $this->assertEquals($expected, $vars->getContent());
    }

    public function testImportDirImporting()
    {
        $expected = array(
            'test_key_1' => array(
                'test_key_2' => 'test_value_2'
            ),
        );

        $vars = new Vars(
            __DIR__ . '/mocks/importing/dir_1.yml',
            array(
                'cache' => false,
            )
        );
        $this->assertEquals($expected, $vars->getContent());
    }

    public function testImportDirImportingNonRecursive()
    {
        $expected = array(
            'test_key_1' => 'test_value_1'
        );

        $vars = new Vars(
            __DIR__ . '/mocks/dir/recursive/test_1.yml',
            array(
                'cache' => false,
            )
        );
        $this->assertEquals($expected, $vars->getContent());
    }

    public function testImportDirImportingRecursiveFlag()
    {
        $vars = new Vars(
            __DIR__ . '/mocks/dir/recursive/flag_1.yml',
            array(
                'cache' => false,
            )
        );
        $this->assertEquals($this->basic_array, $vars->getContent());
    }

    public function testImportStringIfElseDirectory()
    {
        $expected = array(
            'test_key_2' => 'test_value_2',
            'test_key_3' => 'test_value_3',
        );

        $vars = new Vars(
            __DIR__ . '/mocks/importing/ifelse_dir_1.yml',
            array(
                'cache' => false,
            )
        );

        $this->assertEquals($expected, $vars->getContent());
    }

    public function testBasicEmptyYml()
    {
        $vars = new Vars(
            __DIR__ . '/mocks/basic/test_empty_1.yml',
            array(
                'cache' => false,
            )
        );

        $this->assertEquals(array(), $vars->getContent());
    }

    public function testBasicEmptyIni()
    {
        $vars = new Vars(
            __DIR__ . '/mocks/basic/test_empty_1.ini',
            array(
                'cache' => false,
            )
        );

        $this->assertEquals(array(), $vars->getContent());
    }

    public function testBasicEmptyToml()
    {
        $vars = new Vars(
            __DIR__ . '/mocks/basic/test_empty_1.toml',
            array(
                'cache' => false,
            )
        );

        $this->assertEquals(array(), $vars->getContent());
    }

    public function testBasicEmptyEnv()
    {
        $vars = new Vars(
            __DIR__ . '/mocks/basic/test_empty_1.env',
            array(
                'cache' => false,
            )
        );

        $this->assertEquals(array(), $vars->getContent());
    }

    public function testBasicEmptyDir()
    {
        $vars = new Vars(
            __DIR__ . '/mocks/dir/empty',
            array(
                'cache' => false,
            )
        );

        $this->assertEquals(array(), $vars->getContent());
    }

    public function testEmptyFolderImport()
    {
        $vars = new Vars(
            __DIR__ . '/mocks/importing/dir_empty_1.yml',
            array(
                'cache' => false,
            )
        );

        $this->assertEquals(array(), $vars->getContent());
    }

    public function testCustomLoaderString()
    {
        $vars = new Vars(
            __DIR__ . '/mocks/loader/test.txt',
            array(
                'cache'   => false,
                'loaders' => 'M1\Vars\Test\Plugin\TextLoader',
            )
        );

        $this->assertEquals($this->basic_array, $vars->getContent());
    }

    public function testDefaultLoadersWithInvalidLoader()
    {
        $vars = new Vars(
            __DIR__ . '/mocks/basic/test_pass_1.yml',
            array(
                'cache'   => false,
                'loaders' => null,
            )
        );

        $this->assertEquals($this->default_loaders_namespace, $vars->loader->getLoaders());
    }

    public function testDefaultLoadersWithEmptyLoaders()
    {
        $vars = new Vars(
            __DIR__ . '/mocks/basic/test_pass_1.yml',
            array(
                'cache'   => false,
                'loaders' => array(),
            )
        );

        $this->assertEquals($this->default_loaders_namespace, $vars->loader->getLoaders());
    }

    public function testDefaultLoaderString()
    {
        $vars = new Vars(
            __DIR__ . '/mocks/basic/test_pass_1.yml',
            array(
                'cache'   => false,
                'loaders' => 'default',
            )
        );

        $this->assertEquals($this->default_loaders_namespace, $vars->loader->getLoaders());
    }

    public function testDefaultWithCustomLoaderArray()
    {
        $expected = array(
            'M1\Vars\Test\Plugin\TextLoader',
            'M1\Vars\Loader\EnvLoader',
            'M1\Vars\Loader\IniLoader',
            'M1\Vars\Loader\JsonLoader',
            'M1\Vars\Loader\PhpLoader',
            'M1\Vars\Loader\TomlLoader',
            'M1\Vars\Loader\YamlLoader',
            'M1\Vars\Loader\XmlLoader',
        );
        $vars = new Vars(
            __DIR__ . '/mocks/basic/test_pass_1.yml',
            array(
                'cache'   => false,
                'loaders' => array(
                    'M1\Vars\Test\Plugin\TextLoader',
                    'default',
                ),
            )
        );

        $this->assertEquals($expected, $vars->loader->getLoaders());
    }

    public function testBuiltInLoadersWithCustom()
    {
        $expected = array(
            'M1\Vars\Test\Plugin\TextLoader',
            'M1\Vars\Loader\IniLoader',
        );
        $vars = new Vars(
            __DIR__ . '/mocks/basic/test_pass_1.ini',
            array(
                'cache'   => false,
                'loaders' => array(
                    'M1\Vars\Test\Plugin\TextLoader',
                    'ini',
                ),
            )
        );

        $this->assertEquals($expected, $vars->loader->getLoaders());
    }

    public function testSetOptions()
    {
        $resource = __DIR__ . '/mocks/basic/test_pass_1.ini';
        $cache_name = sprintf('%s.php', md5(serialize($resource)));
        $path = __DIR__ . '/mocks/cache';
        $cache_provide = true;
        $cache_path = __DIR__ . '/mocks/cache/output';
        $cache_expire = 1000;

        $vars = new Vars(
            $resource,
            array(
                'path'    => $path,
                'cache'        => $cache_provide,
                'cache_path'   => $cache_path,
                'cache_expire' => $cache_expire,
            )
        );

        $cache = $vars->getCache();

        $this->assertInstanceOf('\M1\Vars\Cache\CacheProvider', $cache);
        $this->assertEquals($cache_provide, $cache->getProvide());
        $this->assertEquals($cache_path, $cache->getPath());
        $this->assertEquals($cache_expire, $cache->getExpire());

        unlink(sprintf('%s/vars/%s', $cache_path, $cache_name));
    }

    public function testSetBasePath()
    {
        $path = __DIR__ . '/mocks/cache';
        $resource = __DIR__ . '/mocks/basic/test_pass_1.ini';
        $cache_name = sprintf('%s.php', md5(serialize($resource)));

        $vars = new Vars(
            $resource,
            array(
                'path' => $path,
            )
        );
        $cache = $vars->getCache();

        $this->assertInstanceOf('\M1\Vars\Cache\CacheProvider', $cache);

        $this->assertEquals($path, $vars->getPath());
        $this->assertEquals($path, $cache->getPath());

        unlink(sprintf('%s/vars/%s', $path, $cache_name));
    }

    public function testVariablesSet()
    {
        $expected = array(
            'test_key_1' => 'test_value_1',
            'test_key_2' => 'test_value_2',
        );

        $vars = new Vars(
            __DIR__ . '/mocks/basic/test_pass_1.yml',
            array(
                'cache'     => false,
                'replacements' => array(
                    'test_key_1' => 'test_value_1',
                    'test_key_2' => 'test_value_2',
                ),
            )
        );

        $this->assertEquals($expected, $vars->variables->rstore->getContent());
    }

    public function testDoReplacementVariables()
    {
        $expected = array(
            'test_key_1' => 'test_value_1_replaced',
            'test_key_2' => 'test_value_2_replaced',
            'test_key_3' => '/foo/inline_replaced/bar',
        );

        $vars = new Vars(
            __DIR__ . '/mocks/variables/basic_1.yml',
            array(
                'cache'     => false,
                'replacements' => array(
                    'test_value_1'       => 'test_value_1_replaced',
                    'test_value_2'       => 'test_value_2_replaced',
                    'inline_replacement' => 'inline_replaced',
                ),
            )
        );

        $this->assertEquals($expected, $vars->getContent());
    }

    public function testDoReplacementEnvironmentVariables()
    {
        $expected = array(
            'test_key_1' => 'test_value_1_from_env',
            'test_key_2' => 'test_value_2_from_env',
        );


        putenv("TEST_ENV_1=test_value_1_from_env");
        putenv("TEST_ENV_2=test_value_2_from_env");

        $vars = new Vars(
            __DIR__ . '/mocks/variables/env_1.yml',
            array(
                'cache'     => false,
            )
        );

        $this->assertEquals($expected, $vars->getContent());
    }

    public function testDoReplacementVariablesFromFile()
    {
        $expected = array(
            'test_key_1' => 'test_value_1_replaced',
            'test_key_2' => 'test_value_2_replaced',
            'test_key_3' => '/foo/inline_replaced/bar',
        );

        $vars = new Vars(
            __DIR__ . '/mocks/variables/basic_1.yml',
            array(
                'cache'     => false,
                'replacements' => __DIR__ . '/mocks/variables/from_file_1.yml',
            )
        );

        $this->assertEquals($expected, $vars->getContent());
    }

    public function testInternalVariables()
    {
        $expected = array(
            "test_key_1" => array(
                "test_key_2" => "test_value_1"
            ),
            "test_import" => array(
                "test_key_3" => "test_value_2"
            ),
            "test_key_4" => "test_value_1",
            "test_key_5" => "test_value_2"
        );

        $vars = new Vars(
            __DIR__ . '/mocks/variables/internal_1.yml',
            array(
                'cache'     => false
            )
        );

        $this->assertEquals($expected, $vars->getContent());
    }

    public function testBasicCache()
    {
        $expected = array(
            'test_key_1' => 'test_value_1',
            'test_key_2' => 'test_value_2',
        );

        $resource = __DIR__ . '/mocks/basic/test_pass_1.json';
        $cache_name = sprintf('%s.php', md5(serialize($resource)));
        $cache_path = __DIR__ . '/mocks/cache/output';

        $vars = new Vars(
            $resource,
            array(
                'cache'      => true,
                'cache_path' => $cache_path,
            )
        );

        $cache = $vars->getCache();
        $this->assertInstanceOf('\M1\Vars\Cache\CacheProvider', $cache);

        $output = $vars->getContent();
        $cache_time = $cache->getTime();

        $this->assertEquals($output, $vars->getContent());

        $vars = new Vars(
            $resource,
            array(
                'cache'      => true,
                'cache_path' => $cache_path,
            )
        );

        $cache = $vars->getCache();
        $this->assertInstanceOf('\M1\Vars\Cache\CacheProvider', $cache);

        $this->assertEquals($output, $vars->getContent());
        $this->assertEquals($cache_time, $cache->getTime());

        unlink(sprintf('%s/vars/%s', $cache_path, $cache_name));
    }

    public function testCacheCheckInResourceProvider()
    {
        $expected = array(
            'test_key_1' => 'test_value_1',
            'test_key_2' => 'test_value_2',
        );

        $resource = __DIR__ . '/mocks/basic/test_pass_1.json';
        $cache_name = sprintf('%s.php', md5(serialize($resource)));
        $cache_path = __DIR__ . '/mocks/basic/';

        $vars = new Vars(
            $resource,
            array(
                'cache'      => true
            )
        );

        $cache = $vars->getCache();
        $this->assertInstanceOf('\M1\Vars\Cache\CacheProvider', $cache);

        $output = $vars->getContent();
        $cache_time = $cache->getTime();

        $this->assertEquals($output, $vars->getContent());

        $vars = new Vars(
            $resource,
            array(
                'cache'      => true
            )
        );

        $cache = $vars->getCache();
        $this->assertInstanceOf('\M1\Vars\Cache\CacheProvider', $cache);

        $this->assertEquals($output, $vars->getContent());
        $this->assertEquals($cache_time, $cache->getTime());

        unlink(sprintf('%s/vars/%s', $cache_path, $cache_name));
    }

    public function testCacheIsCreated()
    {
        $resource = __DIR__ . '/mocks/basic/test_pass_1.yml';
        $cache_name = sprintf('%s.php', md5(serialize($resource)));
        $cache_path = __DIR__ . '/mocks/cache/output';

        $vars = new Vars(
            $resource,
            array(
                'cache'      => true,
                'cache_path' => $cache_path,
            )
        );

        $this->assertTrue(is_file(sprintf('%s/vars/%s', $cache_path, $cache_name)));

        unlink(sprintf('%s/vars/%s', $cache_path, $cache_name));
    }

    public function testCachePathIsSet()
    {
        $resource = __DIR__ . '/mocks/basic/test_pass_1.yml';
        $cache_name = sprintf('%s.php', md5(serialize($resource)));
        $cache_path =  __DIR__ . '/mocks/basic';
        $vars = new Vars(
            $resource,
            array(
                'cache'      => true,
            )
        );

        $this->assertTrue(is_file(sprintf('%s/vars/%s', $cache_path, $cache_name)));
        $this->assertEquals($cache_path, $vars->getCache()->getPath());

        unlink(sprintf('%s/vars/%s', $cache_path, $cache_name));
    }
    public function testGetResourceContent()
    {
        $expected = array(
            'test_key_2' => 'test_value_2',
        );

        $vars = new Vars(
            __DIR__ . '/mocks/basic/test_pass_1.yml',
            array(
                'cache' => false,
            )
        );

        $this->assertEquals($expected, $vars->getResource('test_pass_2.yml')->getContent());
    }

    public function testGetResourceRawContent()
    {
        $expected = array(
            'test_key_2' => 'test_value_2',
        );

        $vars = new Vars(
            __DIR__ . '/mocks/basic/test_pass_1.yml',
            array(
                'cache' => false,
            )
        );

        $this->assertEquals($expected, $vars->getResource('test_pass_2.yml')->getRawContent());
    }

    public function testGetResourceNonExistent()
    {
        $vars = new Vars(
            __DIR__ . '/mocks/basic/test_pass_1.yml',
            array(
                'cache' => false,
            )
        );

        $this->assertFalse($vars->getResource('NON_EXISTENT_RESOURCE'));
    }

    public function testBasicVarsSetContent()
    {
        $expected = array(
            'new_test_key_1' => 'new_test_value_1',
        );

        $vars = new Vars(
            __DIR__ . '/mocks/basic/test_pass_1.yml',
            array(
                'cache' => false,
            )
        );

        $vars->setContent($expected);

        $this->assertEquals($expected, $vars->getContent());
    }

    public function testBasicVarsGet()
    {
        $vars = new Vars(
            __DIR__ . '/mocks/basic/test_pass_1.yml',
            array(
                'cache' => false,
            )
        );

        $this->assertEquals('test_value_1', $vars->get('test_key_1'));
        $this->assertEquals('test_value_1', $vars['test_key_1']);
    }

    public function testDotNotationVarsGet()
    {
        $vars = new Vars(
            __DIR__ . '/mocks/importing/multi_array_1.yml',
            array(
                'cache' => false,
            )
        );

        $this->assertEquals('test_value_2', $vars->get('test_key_1.test_key_2'));
        $this->assertEquals('test_value_2', $vars['test_key_1.test_key_2']);
    }

    public function testBasicVarsSet()
    {
        $vars = new Vars(
            __DIR__ . '/mocks/basic/test_pass_1.yml',
            array(
                'cache' => false,
            )
        );

        $vars->set('test_key_1', 'test_value_1_changed');
        $this->assertEquals('test_value_1_changed', $vars->get('test_key_1'));

        $vars['test_key_1'] = 'test_value_1_changed_2';
        $this->assertEquals('test_value_1_changed_2', $vars['test_key_1']);

        $vars['new_key_1'] = 'new_value_1';
        $this->assertEquals('new_value_1', $vars->get('new_key_1'));

        $vars[null] = 'new_value_1';
        $this->assertEquals('new_value_1', $vars->getContent());
    }

    public function testDotNotationVarsSet()
    {
        $vars = new Vars(
            __DIR__ . '/mocks/importing/multi_array_1.yml',
            array(
                'cache' => false,
            )
        );

        $vars->set('test_key_1.test_key_2', 'test_value_2_changed');
        $this->assertEquals('test_value_2_changed', $vars->get('test_key_1.test_key_2'));

        $vars['test_key_1.test_key_2'] = 'test_value_2_changed_2';
        $this->assertEquals('test_value_2_changed_2', $vars['test_key_1.test_key_2']);

        $vars['new_key_1.new_key_2'] = 'new_value_2';
        $this->assertEquals('new_value_2', $vars->get('new_key_1.new_key_2'));
    }

    public function testBasicUnset()
    {
        $vars = new Vars(
            __DIR__ . '/mocks/basic/test_pass_1.yml',
            array(
                'cache' => false,
            )
        );

        unset($vars['test_key_1']);

        $this->assertTrue($vars->get('test_key_1') === null);
        $this->assertFalse(isset($vars['test_key_1']));
    }

    public function testDotNotationVarsUnset()
    {
        $vars = new Vars(
            __DIR__ . '/mocks/importing/multi_array_1.yml',
            array(
                'cache' => false,
            )
        );

        unset($vars['test_key_1.test_key_2']);

        $this->assertTrue($vars->get('test_key_1.test_key_2') === null);
        $this->assertFalse(isset($vars['test_key_1.test_key_2']));
    }

    public function testGlobalsMerge()
    {
        $vars = new Vars(
            __DIR__ . '/mocks/globals/globals.yml',
            array(
                'cache' => false,
            )
        );

        $this->assertEquals($vars->getContent(), $this->basic_array);
    }

    public function testGlobalsMergeFalse()
    {
        $expected = array(
            'test_key_2' => 'test_value_2'
        );

        $expected_globals = array(
            'test_key_1' => 'test_value_1'
        );

        $vars = new Vars(
            __DIR__ . '/mocks/globals/globals.yml',
            array(
                'cache' => false,
                'merge_globals' => false
            )
        );

        $this->assertEquals($vars->getContent(), $expected);
        $this->assertEquals($vars->getGlobals(), $expected_globals);
    }

    public function testBasicSilexServiceProvider()
    {
        $app = new Application();

        $app->register(new VarsServiceProvider(__DIR__ . '/mocks/basic/test_pass_1.yml'), array(
            'vars.options' => array(
                'cache' => false,
            ),
        ));

        $this->assertEquals($this->basic_array, $app['vars']->getContent());
    }

    public function testOptionsSilexServiceProvider()
    {
        $resource = __DIR__ . '/mocks/basic/test_pass_1.yml';
        $cache_name = sprintf('%s.php', md5(serialize($resource)));
        $path = __DIR__ . '/mocks/cache';
        $cache_provide = true;
        $cache_path = __DIR__ . '/mocks/cache/output';
        $cache_expire = 1000;

        $app = new Application();

        $app->register(
            new VarsServiceProvider($resource),
            array(
                'vars.path'    => $path,
                'vars.options' => array(
                    'cache'        => $cache_provide,
                    'cache_path'   => $cache_path,
                    'cache_expire' => $cache_expire,
                ),
            )
        );

        $this->assertEquals($this->basic_array, $app['vars']->getContent());
        $this->assertEquals($path, $app['vars']->getPath());

        $cache = $app['vars']->getCache();
        $this->assertInstanceOf('\M1\Vars\Cache\CacheProvider', $cache);

        $this->assertEquals($cache_provide, $cache->getProvide());
        $this->assertEquals($cache_path, $cache->getPath());
        $this->assertEquals($cache_expire, $cache->getExpire());

        unlink(sprintf('%s/vars/%s', $cache_path, $cache_name));
    }

    public function testSilexGlobalsMerge()
    {
        $app = new Application();

        $app->register(new VarsServiceProvider(__DIR__ . '/mocks/globals/globals.yml'), array(
            'vars.options' => array(
                'cache' => false,
            ),
        ));
        $app['vars.merge']();

        $this->assertEquals('test_value_1', $app['test_key_1']);
        $this->assertEquals('test_value_2', $app['vars.test_key_2']);
    }

    public function testDebugSilexServiceProvider()
    {
        $resource = __DIR__ . '/mocks/provider/test_1.yml';

        $app = new Application();
        $app['debug'] = true;

        $app->register(
            new VarsServiceProvider($resource),
            array()
        );

        $this->assertFalse($app['vars']->getCache()->getProvide());
    }

    public function testToEnvTransformer()
    {
        $expected = array(
            "test_key_1.test_key_2" => "value",
            "test_key_1.test_key_3" => "value",
            "test_key_4" => "value"
        );

        $vars = new Vars(
            __DIR__ . '/mocks/env/test_1.yml',
            array(
                'cache' => false,
            )
        );

        $vars->toEnv();
        $this->assertEquals($expected, $vars->toDots());
        $this->assertEquals("value", getenv('test_key_1.test_key_2'));
        $this->assertEquals("value", getenv('test_key_1.test_key_3'));
        $this->assertEquals("value", getenv('test_key_4'));
    }

    public function testInvalidResource()
    {
        $this -> expectException(InvalidArgumentException::class);
        $vars = new Vars(new stdClass());
    }

    public function testInvalidLoader()
    {
        $this -> expectException(InvalidArgumentException::class);
        $vars = new Vars(__DIR__ . '/mocks/basic/test_pass_1.yml', array(
            'cache'   => false,
            'loaders' => 'Foo\Bar\FooBarLoader',
        ));

        $this->assertEquals($this->basic_array, $vars->getContent());
    }

    public function testNoLoaderExtensions()
    {
        $this -> expectException(RuntimeException::class);
        $vars = new Vars(
            __DIR__ . '/mocks/loader/test.txt',
            array(
                'cache'   => false,
                'loaders' => 'M1\Vars\Test\Plugin\TextNoExtensionLoader',
            )
        );
    }

    public function testNoLoaderForExtension()
    {
        $this -> expectException(InvalidArgumentException::class);
        $vars = new Vars(__DIR__ . '/mocks/basic/test_pass_1.yml', array(
            'cache'   => false,
            'loaders' => array(
                'ini',
            ),
        ));
    }

    public function testBasicInvalidYML()
    {
        $this -> expectException(RuntimeException::class);
        $vars = new Vars(
            __DIR__ . '/mocks/basic/test_fail_1.yml',
            array(
                'cache' => false,
            )
        );
    }

    /**
     * @requires PHP 5.5
     *
     */
    public function testBasicInvalidIni()
    {
        $this -> expectException(RuntimeException::class);
        // hack for hhvm
        if (defined('HHVM_VERSION')) {
            throw new RuntimeException();
            return;
        }

        $vars = new Vars(
            __DIR__ . '/mocks/basic/test_fail_1.ini',
            array(
                'cache' => false,
            )
        );
    }

    public function testBasicInvalidXml()
    {
        $this -> expectException(RuntimeException::class);
        $vars = new Vars(
            __DIR__ . '/mocks/basic/test_fail_1.xml',
            array(
                'cache' => false,
            )
        );
    }

    public function testBasicInvalidJson()
    {
        $this -> expectException(RuntimeException::class);
        $vars = new Vars(
            __DIR__ . '/mocks/basic/test_fail_1.json',
            array(
                'cache' => false,
            )
        );
    }

    public function testBasicInvalidPhp()
    {
        $this -> expectException(RuntimeException::class);
        $vars = new Vars(
            __DIR__ . '/mocks/basic/test_fail_1.php',
            array(
                'cache' => false,
            )
        );
    }

    public function testBasicInvalidToml()
    {
        $this -> expectException(RuntimeException::class);
        $vars = new Vars(
            __DIR__ . '/mocks/basic/test_fail_1.toml',
            array(
                'cache' => false,
            )
        );
    }

    public function testBasicInvalidEnv()
    {
        $this -> expectException(RuntimeException::class);
        $vars = new Vars(
            __DIR__ . '/mocks/basic/test_fail_1.env',
            array(
                'cache' => false,
            )
        );
    }

    public function testNonexistentFile()
    {
        $this -> expectException(InvalidArgumentException::class);
        $vars = new Vars(
            __DIR__ . '/mocks/FILE_NON_EXISTENT.php',
            array(
                'cache' => false,
            )
        );
    }

    public function testNonexistentImportedFile()
    {
        $this -> expectException(InvalidArgumentException::class);
        $vars = new Vars(
            __DIR__ . '/mocks/FILE_NON_EXISTENT.php',
            array(
                'cache' => false,
            )
        );
    }

    public function testNonexistentImportedIfElseFile()
    {
        $this -> expectException(InvalidArgumentException::class);
        $vars = new Vars(
            __DIR__ . '/mocks/importing/ifelse_5.yml',
            array(
                'cache' => false,
            )
        );
    }

    public function testNonexistentFolder()
    {
        $this -> expectException(InvalidArgumentException::class);
        $vars = new Vars(
            __DIR__ . '/mocks/FOLDER_NON_EXISTENT/',
            array(
                'cache' => false,
            )
        );
    }

    public function testNoneExistentFolderImport()
    {
        $this -> expectException(InvalidArgumentException::class);
        $vars = new Vars(
            __DIR__ . '/mocks/importing/dir_fail_1.yml',
            array(
                'cache' => false,
            )
        );
    }

    public function testNoneExistentBasePath()
    {
        $this -> expectException(InvalidArgumentException::class);
        $vars = new Vars(
            __DIR__ . '/mocks/importing/dir_fail_1.yml',
            array(
                'path' => __DIR__ . '/mocks/FOLDER_NON_EXISTENT',
                'cache'     => false,
            )
        );
    }

    public function testNoneExistentCachePath()
    {
        $this -> expectException(InvalidArgumentException::class);
        $vars = new Vars(
            __DIR__ . '/mocks/importing/dir_fail_1.yml',
            array(
                'cache_path' => __DIR__ . '/mocks/FOLDER_NON_EXISTENT',
                'cache'      => true,
            )
        );
    }

    public function testDoReplacementVariablesFromNonExistentFile()
    {
        $this -> expectException(RuntimeException::class);
        $vars = new Vars(
            __DIR__ . '/mocks/variables/basic_1.yml',
            array(
                'cache'     => false,
                'replacements' => __DIR__ . '/mocks/FILE_NON_EXISTENT',
            )
        );
    }

    public function testNonExistentVariable()
    {
        $this -> expectException(InvalidArgumentException::class);
        $vars = new Vars(
            __DIR__ . '/mocks/variables/fail_1.yml',
            array(
                'cache'     => false,
            )
        );
    }

    public function testFileTraitValidate()
    {
        $this -> expectException(RuntimeException::class);
        $fake_file_resource = new FakeFileResource(__DIR__ . '/mocks/FAKE_FILE');
    }
}
