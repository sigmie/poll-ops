<?php

declare(strict_types=1);

namespace Sigmie\Promises;

use Closure;
use Sigmie\Promises\Contracts\Promise as PromiseInterface;
use Sigmie\Promises\Exceptions\UnknownPromiseResponse;
use Sigmie\Promises\Exceptions\PromiseRejection;

abstract class AbstractPromise
{
    private ?AbstractPromise $successor = null;

    /**
     * Method implementing the CoR pattern for
     * creating promise chains
     *
     * @param PromiseInterface $successor
     * @return void
     */
    final public function setSuccessor(PromiseInterface $successor): void
    {
        if ($this->successor === null) {
            $this->successor = $successor;
            return;
        }

        $this->successor->setSuccessor($successor);
    }

    final public function handle($args = null, $then, Closure $catch)
    {
        $next = ($this->successor !== null)
            ? fn ($args) => $this->successor->handle($args, $then, $catch)
            : fn () => new Settled();

        $catch  = fn (string $reason) => new Rejected($catch, new PromiseRejection($reason));

        $response = $this->execute(
            $args,
            fn () => new Pending(func_get_args(), $catch, $this),
            $catch
        );

        if ($response instanceof Pending) {
            $response = $response->settle($this);
        }

        if ($response instanceof Fulfilled) {
            $response = $next($response->params());
        }

        if ($response instanceof Rejected) {
            $response = $response->reject();
        }

        if ($response instanceof Settled) {
            return $response;
        }

        // Promise execute methods should always return the
        // resolve or the reject method in order for the
        // chain to be aware of what to do next
        throw new UnknownPromiseResponse();
    }

    /**
     * Promise execution
     *
     * @param array $args
     * @param Closure $resolve
     * @param Closure $reject
     *
     * @return Pending|Rejected
     */
    abstract public function execute(array $args, Closure $resolve, Closure $reject);

    abstract public function verify(): bool;

    abstract public function maxAttempts(): int;

    abstract public function attemptsInterval(): int;

    abstract public function exceptionMessage(): string;
}
