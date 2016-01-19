<?php

namespace M1\Vars\Test\Plugin;

use M1\Vars\Loader\AbstractLoader;

class TextNoExtensionLoader extends AbstractLoader
{
    public static $supported = array();

    public function load()
    {
        $content = [];

        foreach (file($this->file) as $line) {
            list($key, $value) = explode(':', $line, 2);
            $content[trim($key)] = trim($value);
        }

        $this->content = $content;

        return $this;
    }
}
