<?php

namespace Bitstone\GuzzleWrapper\Exceptions;

class RedirectException extends HttpException
{
    protected $codes = array(
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect'
    );
}