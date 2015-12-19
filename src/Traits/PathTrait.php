<?php
/**
 * Created by PhpStorm.
 * User: miles
 * Date: 19/12/15
 * Time: 11:32
 */

namespace M1\Vars\Traits;

trait PathTrait
{
    /**
     * The base path for the Vars config and cache folders
     *
     * @var string $path
     */
    public $path;

    /**
     * Get the path
     *
     * @return string The path
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set the Vars base path
     *
     * @param string $path The  path to set
     *
     * @throws \InvalidArgumentException If the path does not exist or is not writable
     *
     * @return \M1\Vars\Vars
     */
    public function setPath($path, $check_writeable = false)
    {
        if (is_null($path)) {
            return;
        }

        if (!is_dir($path) || ($check_writeable && !is_writable($path))) {
            throw new \InvalidArgumentException(sprintf(
                "'%s' base path does not exist or is not writable",
                $path
            ));
        }

        $this->path = realpath($path);
        return $this;
    }
}