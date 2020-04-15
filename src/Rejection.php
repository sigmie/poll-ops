<?php

declare(strict_types=1);

namespace Sigmie\Promises;

use Closure;
use Exception;

class Rejection
{
    private Closure $rejection;

    private Exception $reason;

    public function __construct(Closure $closure, Exception $reason)
    {
        $this->rejection = $closure;
        $this->reason = $reason;
    }

    public function reject()
    {
        return ($this->rejection)($this->reason);
    }
}
