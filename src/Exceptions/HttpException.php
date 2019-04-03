<?php

namespace Bitstone\GuzzleWrapper\Exceptions;

use Exception;

class HttpException extends Exception
{
    protected $codes;

    public function __construct($code, $previousMessage = '', $stackTrace = '')
    {
        $message = $code . ': '. $this->codes[$code];

        if ($previousMessage) {
            $message .= "\n" . $previousMessage . "\n";
        }

        if ($stackTrace) {
            $message .= "\n" . $stackTrace . "\n";
        }

        parent::__construct($message, $code);
    }
}
