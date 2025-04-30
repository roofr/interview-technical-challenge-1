<?php

namespace app\Exceptions;

use Exception;
use Illuminate\Http\Response;

class ApiException extends Exception
{
    public function __construct($message, $code = Response::HTTP_BAD_REQUEST, Exception $previous = null){
        parent::__construct($message, $code, $previous);
    }
}
