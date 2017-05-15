<?php

namespace Bitstone\GuzzleWrapper;

use Illuminate\Support\Facades\Facade;

class Http extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'http';
    }
}