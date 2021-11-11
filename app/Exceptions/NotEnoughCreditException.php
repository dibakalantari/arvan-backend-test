<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class NotEnoughCreditException extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->message = "Not Enough Credit";
        $this->code = 403;
    }
}
