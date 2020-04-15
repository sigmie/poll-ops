<?php

declare(strict_types=1);

namespace Sigmie\Promises;

use Closure;
use Exception;

class PromiseAll
{
    private array $chain = [];

    private ?Closure $catch = null;

    private ?Closure $then = null;

    public function __construct(array $promises = [])
    {
        $this->chain = $promises;
    }

    public function catch(Closure $closure)
    {
        $this->catch = $closure;

        return $this;
    }

    public function then(Closure $closure)
    {
        $this->then = $closure;
    }

    public function proceed($argument)
    {
        if ($this->catch === null) {
            throw new Exception('Unhandled rejection');
        }

        $first = null;

        /** @var AbstractPromise $promise */
        foreach ($this->chain as $successor) {
            if ($first === null) {
                $first = $successor;
                continue;
            }

            $first->setSuccessor($successor);
        }

        return $first->handle($argument, $this->then, $this->catch);
    }
}
