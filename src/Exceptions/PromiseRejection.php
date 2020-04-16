<?php

declare(strict_types=1);

namespace Sigmie\Promises\Exceptions;

use Exception;

class PromiseRejection extends Exception
{
    public function __construct(string $message, ?int $code = null, ?Exception $previous = null)
    {
        parent::__construct($message, $code ?? 0, $previous);
    }
}
