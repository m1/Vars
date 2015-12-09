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
 * @version     0.1.0
 * @author      Miles Croxford <hello@milescroxford.com>
 * @copyright   Copyright (c) Miles Croxford <hello@milescroxford.com>
 * @license     http://github.com/m1/vars/blob/master/LICENSE
 * @link        http://github.com/m1/vars/blob/master/README.MD Documentation
 */

namespace M1\Vars\Resource;

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
     * The env separator for environment replacements
     *
     * @var string
     */
    private static $env_separator = '_ENV::';

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
     * The file resource constructor to get and parse the content from files
     *
     * @param \M1\Vars\Resource\ResourceProvider $provider The parent ResourceProvider
     * @param string                             $file     The passed file
     */
    public function __construct(ResourceProvider $provider, $file)
    {
        $this->provider = $provider;
        $this->vars = $provider->vars;

        $this->makePaths($file);
        $this->validate();

        $content = $this->loadContent($this->file);
        $this->raw_content = $content;

        if ($content) {
            $this->content = $this->searchForResources($content);
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

        $base_path = $this->provider->vars->getBasePath();

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
     * @param mixed $content The file content received from the loader
     *
     * @return array Returns the parsed content
     */
    private function searchForResources($content = array())
    {
        $returned_content = array();

        foreach ($content as $ck => $cv) {
            if ($ck === 'imports' && !is_null($cv) && !empty($cv)) {
                $imported_resource = $this->useImports($cv);

                if ($imported_resource) {
                    $returned_content = array_replace_recursive($returned_content, $imported_resource);
                }

            } elseif (is_array($cv)) {
                $returned_content[$ck] = $this->searchForResources($cv);
            } else {
                $returned_content[$ck] = $this->parseText($cv);
            }
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
        if (substr($text, 0, 6) === self::$env_separator) {
            $variable = trim(substr($text, strlen(self::$env_separator)));

            if ($variable) {
                $value = getenv($variable);

                if ($value) {
                    return $value;
                }
            }
        } else {
            return strtr($text, $this->vars->getVariables());
        }

        return null;
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
            $imported_resources = $this->processImport($import, $imported_resources);
        }

        return $imported_resources;
    }

    /**
     * Processes the imports and gets individual imports and passes them off to import2Resources()
     *
     * @param mixed $imports The imports to be processed
     * @param array $imported_resources The array of imported resourcesg
     *
     * @return array The parsed imported resources
     */
    private function processImport($import, array $imported_resources)
    {
        if (is_array($import) && array_key_exists('resource', $import) && is_array($import['resource'])) {
            foreach ($import['resource'] as $resource) {
                $temp = array(
                    'resource' => $resource,
                    'relative' => $this->isRelative($import)
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
     * @return array|\M1\Vars\Resource\ResourceProvider The resource of the import
     */
    private function createResource($import)
    {
        if (is_array($import) && array_key_exists('resource', $import)) {
            $import_resource = $import;
            $import_resource['relative'] = $this->isRelative($import_resource);
        } elseif (is_string($import)) {
            $import_resource = array('resource' => $import, 'relative' => true);
        } else {
            return false;
        }

        $import_resource = new ResourceProvider(
            $this->provider->vars,
            sprintf('%s/%s', dirname($this->file), $import_resource['resource']),
            $import_resource['relative']
        );

        return $import_resource;
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
     * Checks if the passed import is wanting to be merged into the parent content or relative content
     *
     * @param mixed $import The passed import
     *
     * @return bool Returns whether wanting to be a relative import
     */
    public function isRelative($import)
    {
        if (array_key_exists('relative', $import)) {
            if (is_bool($import['relative'])) {
                return $import['relative'];

            } elseif (is_string($import['relative'])) {
                switch (strtolower($import['relative'])) {
                    case 'false':
                    case 'no':
                        return false;
                    case 'true':
                    case 'yes':
                    default:
                        return true;
                }
            }
        }

        return true;
    }

    /**
     * Returns the filename of the resource
     *
     * @return mixed The filename
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
