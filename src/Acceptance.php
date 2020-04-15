<?php

declare(strict_types=1);

namespace Sigmie\Promises;

use Closure;

class Acceptance
{
    private Closure $resolve;

    private array $params;

    public function __construct(Closure $closure, array $params)
    {
        $this->resolve = $closure;
        $this->params = $params;
    }

    public function resolve()
    {
        return ($this->resolve)(...$this->params);
    }
}
