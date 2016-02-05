<?php

/**
 * This file is part of the m1\vars library
 *
 * Copyright (c) Miles Croxford <hello@milescroxford.com>
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

namespace M1\Vars\Resource;

use M1\Vars\Traits\FileTrait;
use M1\Vars\Traits\ResourceFlagsTrait;
use Symfony\Component\Filesystem\Filesystem;

/**
 * File Resource enables interaction with files as resources
 *
 * @since 0.1.0
 */
class FileResource extends AbstractResource
{
    /**
     * Basic file interaction logic
     */
    use FileTrait;

    /**
     * Enables the resource flags logic
     */
    use ResourceFlagsTrait;

    /**
     * The filename of the loaded file
     *
     * @var string
     */
    private $filename;

    /**
     * The parent ResourceProvider
     *
     * @var \M1\Vars\Resource\ResourceProvider
     */
    private $provider;

    /**
     * The raw content from the passed file
     *
     * @var mixed
     */
    private $raw_content = array();

    /**
     * The VariableProvider
     *
     * @var \M1\Vars\Variables\VariableProvider
     */
    private $variables;

    /**
     * The file resource constructor to get and parse the content from files
     *
     * @param \M1\Vars\Resource\ResourceProvider $provider The parent ResourceProvider
     * @param string                             $file     The passed file
     */
    public function __construct(ResourceProvider $provider, $file)
    {
        $this->provider = $provider;
        $this->vars = $provider->vars;
        $this->variables = $this->vars->variables;

        $this->makePaths($file);
        $this->validate();

        $store_prefix = $this->variables->vstore->getPrefix();

        $content = $this->loadContent($this->file);
        $this->raw_content = $content;

        if ($content) {
            $this->content = $this->searchForResources($content, $store_prefix);
        }
    }

    /**
     * Make the paths used for the filename variable
     *
     * @param string $file The passed file
     */
    private function makePaths($file)
    {
        $file = realpath($file);

        $base_path = $this->provider->vars->getPath();

        $filesystem = new Filesystem();
        $abs_path = $filesystem->makePathRelative(
            $file,
            $base_path
        );

        $this->file = $file;
        $this->filename = rtrim($abs_path, "/");
    }

    /**
     * Search for imports in the files and does the replacement variables
     *
     * @param mixed  $content The file content received from the loader
     * @param string $prefix  The array prefix for the entity
     *
     * @return array Returns the parsed content
     */
    private function searchForResources($content = array(), $prefix = '')
    {
        $returned_content = array();

        foreach ($content as $ck => $cv) {
            $this->variables->vstore->setCurrentPrefix($prefix);
            $returned_content = $this->parseContent($ck, $cv, $returned_content, $prefix);
        }

        return $returned_content;
    }

    /**
     * Parses the contents inside the content array
     *
     * @param mixed  $key              The key of the content array
     * @param mixed  $value            The value of the key
     * @param array  $returned_content The modified content array to return
     * @param string $prefix           The array prefix for the entity
     *
     * @return array Returns the modified content array
     */
    private function parseContent($key, $value, $returned_content, $prefix)
    {
        if ($key === 'imports' && !is_null($value) && !empty($value)) {
            $imported_resource = $this->useImports($value);

            if ($imported_resource) {
                $returned_content = array_replace_recursive($returned_content, $imported_resource);
            }
        } elseif (is_array($value)) {
            $returned_content[$key] = $this->searchForResources(
                $value,
                $this->variables->vstore->createPrefixName($prefix, $key)
            );
        } else {
            $value = $this->parseText($value);
            $this->variables->vstore->set($prefix.$key, $value);
            $returned_content[$key] = $value;
        }

        return $returned_content;
    }

    /**
     * Parses the text for option and environment replacements and replaces the text
     *
     * @param string $text The text to be parsed
     *
     * @return string|null The parsed string
     */
    private function parseText($text)
    {
        if (is_string($text)) {
            return $this->variables->parse($text);
        }

        return $text;
    }

    /**
     * Use the import arrays to import resources
     *
     * @param mixed $imports The resources wanting to be imported
     *
     * @return array The parsed imported resources
     */
    private function useImports($imports)
    {
        $imported_resources = array();

        if ((is_array($imports) && $this->isAssoc($imports)) || is_string($imports)) {
            $imports = array($imports);
        }

        foreach ($imports as $import) {
            $imported_resources = $this->processImport($this->parseText($import), $imported_resources);
        }

        return $imported_resources;
    }

    /**
     * Processes the import and gets individual import if set and passes them off to import2Resources()
     *
     * @param mixed $import The import to be processed
     * @param array $imported_resources The array of imported resources
     *
     * @return array The parsed imported resources
     */
    private function processImport($import, array $imported_resources)
    {
        if (is_array($import) && array_key_exists('resource', $import) && is_array($import['resource'])) {
            foreach ($import['resource'] as $resource) {
                $temp = array(
                    'resource' => $resource,
                    'relative' => $this->checkBooleanValue('relative', $import),
                    'recursive' => $this->checkBooleanValue('recursive', $import),
                );

                $imported_resources = $this->import2Resource($temp, $imported_resources);
            }
        } else {
            $imported_resources = $this->import2Resource($import, $imported_resources);
        }

        return $imported_resources;
    }

    /**
     * Creates the resource from the import then imports it
     *
     * @param array|string $import The string|array to be converted to a resource
     * @param array $imported_resources The array of imported resources
     *
     * @return array The imported resources
     */
    private function import2Resource($import, array $imported_resources)
    {
        $resource = $this->createResource($import);

        if ($resource) {
            $imported_resources = $this->importResource($resource, $imported_resources);
        }

        return $imported_resources;
    }

    /**
     * Creates resource from the import
     *
     * @param array|string $import The import to create a resource from
     *
     * @return \M1\Vars\Resource\ResourceProvider The resource of the import
     */
    private function createResource($import)
    {
        if (is_array($import) && array_key_exists('resource', $import)) {
            $import_resource = $import;
            $import_resource['relative'] = $this->checkBooleanValue('relative', $import_resource);
            $import_resource['recursive'] = $this->checkBooleanValue('recursive', $import_resource);
        } elseif (is_string($import)) {
            $import_resource = array('resource' => $import, 'relative' => true, 'recursive' => true);
        }

        $import_resource = new ResourceProvider(
            $this->provider->vars,
            $this->createImportName($import_resource['resource']),
            $import_resource['relative'],
            $import_resource['recursive']
        );

        return $import_resource;
    }

    /**
     * Creates the correctly formatted resource name with paths
     *
     * @param string $resource The resource to create the import name for
     *
     * @return string The parsed resource
     */
    private function createImportName($resource)
    {
        $resource = $this->explodeResourceIfElse($resource);
        $resource_pieces = array();
        
        foreach ($resource as $r) {
            $parsed_r = $this->trimFlags($r);
            $parsed_r = sprintf('%s/%s', dirname($this->file), $parsed_r);
            $parsed_r = $this->replicateFlags($parsed_r, $r);

            $resource_pieces[] = $parsed_r;
        }

        return $this->implodeResourceIfElse($resource_pieces);
    }

    /**
     * Import resource into the imported resources and merge contents
     *
     * @param ResourceProvider $provider The new imported resource
     * @param array            $imported_resources The imported resources
     *
     * @return array The modified imported resources
     */
    private function importResource(ResourceProvider $provider, $imported_resources)
    {
        $content = $provider->getContent();
        $parent_content = $provider->getParentContent();

        if (!empty($content)) {
            $imported_resources = array_replace_recursive($imported_resources, $content);
        }

        if (!empty($parent_content)) {
            $this->provider->addParentContent($parent_content);
        }

        return $imported_resources;
    }

    /**
     * Returns whether the passed array is associative
     *
     * @param array $array The passed array
     *
     * @return bool Is the passed array associative
     */
    private function isAssoc(array $array)
    {
        return array_keys($array) !== range(0, count($array) - 1);
    }

    /**
     * Checks if the passed boolean value is true or false
     *
     * @param string $value  The value to check
     * @param mixed  $import The passed import
     *
     * @return bool Returns the value of the boolean
     */
    public function checkBooleanValue($value, $import)
    {
        $default = false;

        if ($value === 'relative') {
            $default = true;
        }

        $value = (isset($import[$value])) ? $import[$value] : $default;

        return $this->getBooleanValue($value);
    }

    /**
     * Gets the boolean value from the string
     *
     * @param string $value  The value to check
     *
     * @return bool Returns the value of the boolean
     */
    private function getBooleanValue($value)
    {
        $value = strtolower($value);

        if (!$value || $value === "false" || $value === "no") {
            return false;
        }

        return true;
    }

    /**
     * Returns the filename of the resource
     *
     * @return string The filename
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Returns the raw content of the resource
     *
     * @return array|mixed The raw content
     */
    public function getRawContent()
    {
        return $this->raw_content;
    }
}
