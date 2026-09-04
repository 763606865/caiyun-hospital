<?php

namespace App\Exceptions;

use Throwable;

class ConflictException extends HttpException
{
    public function __construct(string $message = '操作冲突.', ?Throwable $previous = null)
    {
        parent::__construct($message, 409, $previous);
    }
}
