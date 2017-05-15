<?php

namespace Bitstone\GuzzleWrapper\Exceptions;

class InformationalException extends HttpException
{
    protected $codes = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing'
    );
}