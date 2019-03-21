<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class VersionExpiredException extends Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        if(empty($message)){
            $message = '本条数据被别人修改过，刷新后重新修改。';
        }
        parent::__construct($message, $code, $previous);
    }
}
