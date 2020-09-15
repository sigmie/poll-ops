<?php

declare(strict_types=1);

namespace Sigmie\PollOps\Exceptions;

use Exception;

class UnhandledRejection extends Exception
{
    /**
     * @param null|string $message
     * @param null|int $code
     * @param null|Exception $previous
     */
    public function __construct(?string $message = null, ?int $code = null, ?Exception $previous = null)
    {
        parent::__construct($message ?? 'Unhandled promise rejection', $code ?? 0, $previous);
    }
}
