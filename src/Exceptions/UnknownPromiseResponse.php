<?php

declare(strict_types=1);

namespace Sigmie\PollOps\Exceptions;

use Exception;

class UnknownPromiseResponse extends Exception
{
    /**
     * @param null|string $message
     * @param null|int $code
     * @param null|Exception $previous
     */
    public function __construct(?string $message = null, ?int $code = null, ?Exception $previous = null)
    {
        parent::__construct($message ?? 'Unknown promise response', $code ?? 0, $previous);
    }
}
