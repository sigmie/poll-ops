<?php

declare(strict_types=1);

namespace Sigmie\PollOps\Exceptions;

use Exception;

class PromiseRejection extends Exception
{
    /**
     * @param string $message
     * @param null|int $code
     * @param null|Exception $previous
     * @return void
     */
    public function __construct(string $message, ?int $code = null, ?Exception $previous = null)
    {
        parent::__construct($message, $code ?? 0, $previous);
    }
}
