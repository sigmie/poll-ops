<?php

declare(strict_types=1);

namespace Sigmie\Promises;

use Closure;
use Exception;

abstract class AbstractPromise
{
    private ?AbstractPromise $successor = null;

    final public function setSuccessor(AbstractPromise $successor)
    {
        if ($this->successor === null) {
            $this->successor = $successor;
            return;
        }

        $this->successor->setSuccessor($successor);
    }

    final public function handle($args = null, $then, Closure $catch)
    {
        $next = ($this->successor === null) ? fn () => new Acceptance($then, func_get_args())
            : fn ($resolve) => $this->successor->handle($resolve, $then, $catch);

        $response = $this->execute(
            $args,
            $next,
            fn (Exception $reason) => new Rejection($catch, $reason)
        );

        if ($response instanceof Rejection) {
            return $response->reject();
        }

        if ($response instanceof Acceptance) {
            return $response->resolve();
        }

        if ($response === null) {
            return;
        }

        return $response;
    }

    abstract public function execute($args, Closure $resolve, Closure $reject);
}
