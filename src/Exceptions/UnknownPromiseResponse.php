<?php

declare(strict_types=1);

namespace Sigmie\Promises\Exceptions;

use Exception;

class UnknownPromiseResponse extends Exception
{
    public function __construct(?string $message = null, ?int $code = null, ?Exception $previous = null)
    {
        parent::__construct($message ?? 'Unknown promise response', $code ?? 0, $previous);
    }
}
