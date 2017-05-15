<?php

namespace Bitstone\GuzzleWrapper\Exceptions;

use Exception;

class HttpException extends Exception
{
    protected $codes;

    public function __construct($code)
    {
        $message = $code . ': '. $this->codes[$code];

        parent::__construct($message, $code);
    }
}