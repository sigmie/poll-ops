<?php

declare(strict_types=1);

namespace Sigmie\Promises;

use Closure;
use Sigmie\Promises\Contracts\Promise as PromiseInterface;

class Promise extends AbstractPromise implements PromiseInterface
{
    private Closure $callable;

    public function __construct(Closure $callable)
    {
        $this->callable = $callable;
    }

    public function execute(Closure $resolve, Closure $reject)
    {
        ($this->callable)($resolve, $reject);
    }
}
