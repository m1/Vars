<?php
/**
 * Created by PhpStorm.
 * User: miles
 * Date: 06/12/15
 * Time: 21:33
 */

namespace M1\Vars\Test\Plugin;

use \M1\Vars\Traits\FileTrait;

class FakeFileResource
{
    use FileTrait;

    public function __construct($resource)
    {
        $this->validate($resource);
    }
}
