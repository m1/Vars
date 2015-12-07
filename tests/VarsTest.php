<?php

namespace M1\Vars\Test;

use M1\Vars\Vars;

class VarsTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->basic_array = array(
            'test_key_1' => 'test_value_1',
            'test_key_2' => 'test_value_2'
        );
    }

    public function testBasicValidYML()
    {
        $vars = new Vars(
            __DIR__.'/mocks/basic/test_pass_1.yml',
            array(
                'cache' => false
            )
        );

            $this->assertEquals($this->basic_array, $vars->getContent());
    }

    public function testBasicValidJson()
    {
        $vars = new Vars(
            __DIR__.'/mocks/basic/test_pass_1.json',
            array(
                'cache' => false
            )
        );

            $this->assertEquals($this->basic_array, $vars->getContent());
    }

    public function testBasicValidPHP()
    {
        $vars = new Vars(
            __DIR__.'/mocks/basic/test_pass_1.php',
            array(
                'cache' => false
            )
        );

            $this->assertEquals($this->basic_array, $vars->getContent());
    }

    public function testBasicValidCallablePHP()
    {
        $vars = new Vars(
            __DIR__.'/mocks/basic/test_pass_3.php',
            array(
                'cache' => false
            )
        );

            $this->assertEquals($this->basic_array, $vars->getContent());
    }

    public function testBasicValidXML()
    {
        $vars = new Vars(
            __DIR__.'/mocks/basic/test_pass_1.xml',
            array(
                'cache' => false
            )
        );

            $this->assertEquals($this->basic_array, $vars->getContent());
    }

    public function testBasicValidToml()
    {
        $vars = new Vars(
            __DIR__.'/mocks/basic/test_pass_1.toml',
            array(
                'cache' => false
            )
        );

            $this->assertEquals($this->basic_array, $vars->getContent());
    }

    public function testBasicValidIni()
    {
        $vars = new Vars(
            __DIR__.'/mocks/basic/test_pass_1.ini',
            array(
                'cache' => false
            )
        );

            $this->assertEquals($this->basic_array, $vars->getContent());
    }

    public function testBasicDir()
    {
        $vars = new Vars(
            __DIR__.'/mocks/dir/',
            array(
                'cache' => false
            )
        );

            $this->assertEquals($this->basic_array, $vars->getContent());
    }

    public function testBasicArray()
    {
        $vars = new Vars(
            array(
            __DIR__.'/mocks/basic/test_pass_1.php',
            __DIR__.'/mocks/basic/test_pass_1.yml',
            ),
            array(
                'cache' => false
            )
        );

            $this->assertEquals($this->basic_array, $vars->getContent());
    }

    public function testBasicImporting()
    {
        $expected = array_merge($this->basic_array, array(
            'test_key_3' => array(
                'test_key_4' => 'test_value_4'
            ),
            'test_key_5' => 'test_value_5'
        ));

        $vars = new Vars(
            __DIR__.'/mocks/importing/basic_1.yml',
            array(
                'cache' => false
            )
        );

            $this->assertEquals($expected, $vars->getContent());
    }

    public function testRelativeResourceArrayImporting()
    {
        $expected = array(
            'test_key_1' => array(
                'test_key_2' => 'test_value_2',
                'test_key_3' => 'test_value_3'
            ),
            'test_key_4' => 'test_value_4'
        );

        $vars = new Vars(
            __DIR__.'/mocks/importing/relative_resource_array_1.yml',
            array(
                'cache' => false
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
                'test_key_4' => 'test_value_4'
            ),
            'test_key_5' => 'test_value_5',
            'test_key_6' => 'test_value_6',
        );

        $vars = new Vars(
            __DIR__.'/mocks/importing/relative_string_1.yml',
            array(
                'cache' => false
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
            __DIR__.'/mocks/importing/multi_array_1.yml',
            array(
                'cache' => false
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
            __DIR__.'/mocks/importing/basic_array_1.yml',
            array(
                'cache' => false
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
            __DIR__.'/mocks/importing/basic_string_1.yml',
            array(
                'cache' => false
            )
        );

            $this->assertEquals($expected, $vars->getContent());
    }

    public function testImportDirImporting()
    {
        $expected = array(
            'test_key_1' => array(
                'test_key_2' => 'test_value_2',
                'test_key_3' => 'test_value_3',
            ),
        );

        $vars = new Vars(
            __DIR__.'/mocks/importing/dir_1.yml',
            array(
                'cache' => false
            )
        );
            $this->assertEquals($expected, $vars->getContent());
    }

    public function testBasicEmptyYml()
    {
        $vars = new Vars(
            __DIR__.'/mocks/basic/test_empty_1.yml',
            array(
                'cache' => false
            )
        );

            $this->assertEquals(array(), $vars->getContent());
    }

    public function testBasicEmptyIni()
    {
        $vars = new Vars(
            __DIR__.'/mocks/basic/test_empty_1.ini',
            array(
                'cache' => false
            )
        );

            $this->assertEquals(array(), $vars->getContent());
    }

    public function testBasicEmptyJson()
    {
        $vars = new Vars(
            __DIR__.'/mocks/basic/test_empty_1.json',
            array(
                'cache' => false
            )
        );

            $this->assertEquals(array(), $vars->getContent());
    }

    public function testBasicEmptyToml()
    {
        $vars = new Vars(
            __DIR__.'/mocks/basic/test_empty_1.toml',
            array(
                'cache' => false
            )
        );

            $this->assertEquals(array(), $vars->getContent());
    }

    public function testBasicEmptyDir()
    {
        $vars = new Vars(
            __DIR__.'/mocks/dir/empty',
            array(
                'cache' => false
            )
        );

            $this->assertEquals(array(), $vars->getContent());
    }

    public function testEmptyFolderImport()
    {
        $vars = new Vars(
            __DIR__.'/mocks/importing/dir_empty_1.yml',
            array(
                'cache' => false
            )
        );

            $this->assertEquals([], $vars->getContent());
    }

    public function testCustomLoaderString()
    {
        $vars = new Vars(
            __DIR__.'/mocks/loader/test.txt',
            array(
                'cache' => false,
                'loaders' => 'M1\Vars\Test\Plugin\TextLoader'
            )
        );

            $this->assertEquals($this->basic_array, $vars->getContent());
    }

    public function testDefaultLoaderString()
    {
        $expected = array(
            'M1\Vars\Loader\IniLoader',
            'M1\Vars\Loader\JsonLoader',
            'M1\Vars\Loader\PhpLoader',
            'M1\Vars\Loader\TomlLoader',
            'M1\Vars\Loader\YamlLoader',
            'M1\Vars\Loader\XmlLoader',
        );
        $vars = new Vars(
            __DIR__.'/mocks/basic/test_pass_1.yml',
            array(
                'cache' => false,
                'loaders' => 'default'
            )
        );

            $this->assertEquals($expected, $vars->getLoaders());
    }

    public function testDefaultWithCustomLoaderArray()
    {
        $expected = array(
            'M1\Vars\Test\Plugin\TextLoader',
            'M1\Vars\Loader\IniLoader',
            'M1\Vars\Loader\JsonLoader',
            'M1\Vars\Loader\PhpLoader',
            'M1\Vars\Loader\TomlLoader',
            'M1\Vars\Loader\YamlLoader',
            'M1\Vars\Loader\XmlLoader',
        );
        $vars = new Vars(
            __DIR__.'/mocks/basic/test_pass_1.yml',
            array(
                'cache' => false,
                'loaders' => array(
                    'M1\Vars\Test\Plugin\TextLoader',
                    'default'
                )
            )
        );

            $this->assertEquals($expected, $vars->getLoaders());
    }

    public function testBuiltInLoadersWithCustom()
    {
        $expected = array(
            'M1\Vars\Test\Plugin\TextLoader',
            'M1\Vars\Loader\IniLoader'
        );
        $vars = new Vars(
            __DIR__.'/mocks/basic/test_pass_1.ini',
            array(
                'cache' => false,
                'loaders' => array(
                    'M1\Vars\Test\Plugin\TextLoader',
                    'ini'
                )
            )
        );

            $this->assertEquals($expected, $vars->getLoaders());
    }

    public function testSetOptions()
    {
        $resource = __DIR__.'/mocks/basic/test_pass_1.ini';
        $cache_name = sprintf('%s.php', md5(serialize($resource)));
        $base_path = __DIR__.'/mocks/cache';
        $cache = true;
        $cache_path = __DIR__.'/mocks/cache/output';
        $cache_expire = 1000;


        $vars = new Vars(
            $resource,
            array(
                'base_path' => $base_path,
                'cache' => $cache,
                'cache_path' => $cache_path,
                'cache_expire' => $cache_expire,
            )
        );

            $this->assertEquals($base_path, $vars->getBasePath());
            $this->assertEquals($cache, $vars->getCache());
            $this->assertEquals($cache_path, $vars->getCachePath());
            $this->assertEquals($cache_expire, $vars->getCacheExpire());

            unlink(sprintf('%s/%s', $cache_path, $cache_name));
    }

    public function testSetBasePath()
    {
        $base_path = __DIR__.'/mocks/cache';
        $resource = __DIR__.'/mocks/basic/test_pass_1.ini';
        $cache_name = sprintf('%s.php', md5(serialize($resource)));

        $vars = new Vars(
            $resource,
            array(
                'base_path' => $base_path,
            )
        );

            $this->assertEquals($base_path, $vars->getBasePath());
            $this->assertEquals($base_path, $vars->getCachePath());

            unlink(sprintf('%s/%s', $base_path, $cache_name));
    }

    public function testVariablesSet()
    {
        $expected = array(
            '%test_key_1%' => 'test_value_1',
            '%test_key_2%' => 'test_value_2',
        );

        $vars = new Vars(
            __DIR__.'/mocks/basic/test_pass_1.yml',
            array(
                'cache' => false,
                'variables' => array(
                    'test_key_1' => 'test_value_1',
                    'test_key_2' => 'test_value_2'
                )
            )
        );

            $this->assertEquals($expected, $vars->getVariables());
    }

    public function testDoReplacementVariables()
    {
        $expected = array(
            'test_key_1' => 'test_value_1_replaced',
            'test_key_2' => 'test_value_2_replaced',
            'test_key_3' => '/foo/inline_replaced/bar'
        );

        $vars = new Vars(
            __DIR__.'/mocks/variables/basic_1.yml',
            array(
                'cache' => false,
                'variables' => array(
                    'test_value_1' => 'test_value_1_replaced',
                    'test_value_2' => 'test_value_2_replaced',
                    'inline_replacement' => 'inline_replaced'
                )
            )
        );

            $this->assertEquals($expected, $vars->getContent());
    }

    public function testDoReplacementVariablesFromFile()
    {
        $expected = array(
            'test_key_1' => 'test_value_1_replaced',
            'test_key_2' => 'test_value_2_replaced',
            'test_key_3' => '/foo/inline_replaced/bar'
        );

        $vars = new Vars(
            __DIR__.'/mocks/variables/basic_1.yml',
            array(
                'cache' => false,
                'variables' => __DIR__.'/mocks/variables/from_file_1.yml'
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

        $resource = __DIR__.'/mocks/basic/test_pass_1.json';
        $cache_name = sprintf('%s.php', md5(serialize($resource)));
        $cache_path = __DIR__.'/mocks/cache/output';

        $vars = new Vars(
            $resource,
            array(
                'cache' => true,
                'cache_path' => $cache_path
            )
        );

            $output = $vars->getContent();
            $cache_time = $vars->getCacheTime();

            $vars = new Vars(
                $resource,
                array(
                'cache' => true,
                'cache_path' => $cache_path,
                )
            );

            $this->assertEquals($output, $vars->getContent());
            $this->assertEquals($cache_time, $vars->getCacheTime());

            unlink(sprintf('%s/%s', $cache_path, $cache_name));
    }

    public function testCacheIsCreated()
    {
        $resource = __DIR__.'/mocks/variables/basic_1.yml';
        $cache_name = sprintf('%s.php', md5(serialize($resource)));
        $cache_path = __DIR__.'/mocks/cache/output';

        $vars = new Vars(
            $resource,
            array(
                'cache' => true,
                'cache_path' => $cache_path,
            )
        );

            $this->assertTrue(is_file(sprintf('%s/%s', $cache_path, $cache_name)));

            unlink(sprintf('%s/%s', $cache_path, $cache_name));
    }

    public function testGetResourceContent()
    {
        $expected = array(
            'test_key_2' => 'test_value_2'
        );

        $vars = new Vars(
            __DIR__.'/mocks/basic/test_pass_1.yml',
            array(
                'cache' => false,
            )
        );

            $this->assertEquals($expected, $vars->getResource('test_pass_2.yml')->getContent());
    }

    public function testGetResourceRawContent()
    {
        $expected = array(
            'test_key_2' => 'test_value_2'
        );

        $vars = new Vars(
            __DIR__.'/mocks/basic/test_pass_1.yml',
            array(
                'cache' => false,
            )
        );

            $this->assertEquals($expected, $vars->getResource('test_pass_2.yml')->getRawContent());
    }

    public function testGetResourceNonExistent()
    {
        $vars = new Vars(
            __DIR__.'/mocks/variables/basic_1.yml',
            array(
                'cache' => false,
            )
        );

            $this->assertFalse($vars->getResource('NON_EXISTENT_RESOURCE'));
    }

    public function testBasicVarsSetContent()
    {
        $expected = array(
            'new_test_key_1' => 'new_test_value_1'
        );

        $vars = new Vars(
            __DIR__.'/mocks/basic/test_pass_1.yml',
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
            __DIR__.'/mocks/basic/test_pass_1.yml',
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
            __DIR__.'/mocks/importing/multi_array_1.yml',
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
            __DIR__.'/mocks/basic/test_pass_1.yml',
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
    }

    public function testDotNotationVarsSet()
    {

        $vars = new Vars(
            __DIR__.'/mocks/importing/multi_array_1.yml',
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
            __DIR__.'/mocks/basic/test_pass_1.yml',
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
            __DIR__.'/mocks/importing/multi_array_1.yml',
            array(
                'cache' => false,
            )
        );

            unset($vars['test_key_1.test_key_2']);

            $this->assertTrue($vars->get('test_key_1.test_key_2') === null);
            $this->assertFalse(isset($vars['test_key_1.test_key_2']));
    }

    public function testBasicSilexServiceProvider()
    {
        $app = new \Silex\Application();

        $app->register(new \M1\Vars\Provider\Silex\VarsServiceProvider(__DIR__.'/mocks/basic/test_pass_1.yml'), array(
            'vars.options' => array(
                'cache' => false
            )
        ));

        $this->assertEquals($this->basic_array, $app['vars']->getContent());

    }

    public function testOptionsSilexServiceProvider()
    {
        $resource = __DIR__.'/mocks/basic/test_pass_1.yml';
        $cache_name = sprintf('%s.php', md5(serialize($resource)));
        $base_path = __DIR__.'/mocks/cache';
        $cache = true;
        $cache_path = __DIR__.'/mocks/cache/output';
        $cache_expire = 1000;

        $app = new \Silex\Application();

        $app->register(
            new \M1\Vars\Provider\Silex\VarsServiceProvider($resource),
            array(
                'vars.path' => $base_path,
                'vars.options' => array(
                    'cache' => $cache,
                    'cache_path' => $cache_path,
                    'cache_expire' => $cache_expire,
                )
            )
        );

            $this->assertEquals($this->basic_array, $app['vars']->getContent());
            $this->assertEquals($base_path, $app['vars']->getBasePath());
            $this->assertEquals($cache, $app['vars']->getCache());
            $this->assertEquals($cache_path, $app['vars']->getCachePath());
            $this->assertEquals($cache_expire, $app['vars']->getCacheExpire());

            unlink(sprintf('%s/%s', $cache_path, $cache_name));
    }

    public function testDebugSilexServiceProvider()
    {
        $resource = __DIR__.'/mocks/provider/test_1.yml';

        $app = new \Silex\Application();
        $app['debug'] = true;

        $app->register(
            new \M1\Vars\Provider\Silex\VarsServiceProvider($resource),
            array(
            )
        );

            $this->assertFalse($app['vars']->getCache());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidResource()
    {
        $vars = new Vars(new \stdClass());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidLoader()
    {
        $vars = new Vars(__DIR__.'/mocks/basic/test_pass_1.yml', array(
            'cache' => false,
            'loaders' => 'Foo\Bar\FooBarLoader',
        ));

        $this->assertEquals($this->basic_array, $vars->getContent());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNoLoaders()
    {
        $vars = new Vars(
            __DIR__.'/mocks/basic/test_pass_1.ini',
            array(
                'cache' => false,
                'loaders' => 1
            )
        );
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testNoLoaderExtensions()
    {
        $vars = new Vars(
            __DIR__.'/mocks/loader/test.txt',
            array(
                'cache' => false,
                'loaders' => 'M1\Vars\Test\Plugin\TextNoExtensionLoader'
            )
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNoLoaderForExtension()
    {
        $vars = new Vars(__DIR__.'/mocks/basic/test_pass_1.yml', array(
            'cache' => false,
            'loaders' => array(
                'ini'
            )
        ));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testBasicInvalidYML()
    {
        $vars = new Vars(
            __DIR__.'/mocks/basic/test_fail_1.yml',
            array(
                'cache' => false
            )
        );
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testBasicInvalidXml()
    {
        $vars = new Vars(
            __DIR__.'/mocks/basic/test_fail_1.xml',
            array(
                'cache' => false
            )
        );
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testBasicInvalidIni()
    {
        $vars = new Vars(
            __DIR__.'/mocks/basic/test_fail_1.ini',
            array(
                'cache' => false
            )
        );
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testBasicInvalidJson()
    {
        $vars = new Vars(
            __DIR__.'/mocks/basic/test_fail_1.json',
            array(
                'cache' => false
            )
        );
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testBasicInvalidPhp()
    {
        $vars = new Vars(
            __DIR__.'/mocks/basic/test_fail_1.php',
            array(
                'cache' => false
            )
        );
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testBasicInvalidToml()
    {
        $vars = new Vars(
            __DIR__.'/mocks/basic/test_fail_1.toml',
            array(
                'cache' => false
            )
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNonexistentFile()
    {
        $vars = new Vars(
            __DIR__.'/mocks/FILE_NON_EXISTENT.php',
            array(
                'cache' => false
            )
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNonexistentImportedFile()
    {
        $vars = new Vars(
            __DIR__.'/mocks/FILE_NON_EXISTENT.php',
            array(
                'cache' => false
            )
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNonexistentFolder()
    {
        $vars = new Vars(
            __DIR__.'/mocks/FOLDER_NON_EXISTENT/',
            array(
                'cache' => false
            )
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNoneExistentFolderImport()
    {
        $vars = new Vars(
            __DIR__.'/mocks/importing/dir_fail_1.yml',
            array(
                'cache' => false
            )
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNoneExistentBasePath()
    {
        $vars = new Vars(
            __DIR__.'/mocks/importing/dir_fail_1.yml',
            array(
                'base_path' => __DIR__.'/mocks/FOLDER_NON_EXISTENT',
                'cache' => false,
            )
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNoneExistentCachePath()
    {
        $vars = new Vars(
            __DIR__.'/mocks/importing/dir_fail_1.yml',
            array(
                'cache_path' => __DIR__.'/mocks/FOLDER_NON_EXISTENT',
                'cache' => false,
            )
        );
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testDoReplacementVariablesFromNonExistentFile()
    {
        $vars = new Vars(
            __DIR__.'/mocks/variables/basic_1.yml',
            array(
                'cache' => false,
                'variables' => __DIR__.'/mocks/FILE_NON_EXISTENT'
            )
        );
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testFileTraitValidate()
    {
        $fake_file_resource = new \M1\Vars\Test\Plugin\FakeFileResource(__DIR__.'/mocks/FAKE_FILE');
    }
}
