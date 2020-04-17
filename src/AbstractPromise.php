<?php

declare(strict_types=1);

namespace Sigmie\Promises;

use Closure;
use Sigmie\Promises\Contracts\Promise as PromiseInterface;
use Sigmie\Promises\Exceptions\PromiseRejection;
use Sigmie\Promises\Exceptions\UnknownPromiseResponse;

abstract class AbstractPromise implements PromiseInterface
{
    /**
     * Promise successor
     *
     * @var null|PromiseInterface
     */
    private ?PromiseInterface $successor = null;

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

    /**
     * Handle promise chain part
     *
     * @param array $args
     * @param Closure $catch
     *
     * @return Settled
     *
     * @throws UnknownPromiseResponse
     */
    final public function handle(array $args, Closure $catch): Settled
    {
        $next = ($this->successor !== null)
            ? fn ($resolveArgs) => $this->successor->handle($resolveArgs, $catch)
            : fn () => new Settled();

        $response = $this->execute(
            $args,
            fn () => new Pending(func_get_args(), $this),
            fn (string $reason) => new Rejected(new PromiseRejection($reason))
        );

        if ($response instanceof Pending) {
            $response = $response->settle();
        }

        if ($response instanceof Fulfilled) {
            $response = $next($response->params());
        }

        if ($response instanceof Rejected) {
            $response = $response->reject($catch);
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

    /**
     * Promise verification
     *
     * @return bool
     */
    abstract public function verify(): bool;

    /**
     * Max promise verification attempts
     *
     * @return int
     */
    abstract public function maxAttempts(): int;

    /**
     * Promise verification attempts interval
     *
     * @return int
     */
    abstract public function attemptsInterval(): int;

    /**
     * Promise rejection exception message
     *
     * @return string
     */
    abstract public function exceptionMessage(): string;
}
