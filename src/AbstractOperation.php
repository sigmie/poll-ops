<?php

declare(strict_types=1);

namespace Sigmie\PollOps;

use Closure;
use Sigmie\PollOps\States\Pending;
use Sigmie\PollOps\States\Fulfilled;
use Sigmie\PollOps\States\Rejected;
use Sigmie\PollOps\States\Settled;
use Sigmie\PollOps\Contracts\Operation as PromiseInterface;
use Sigmie\PollOps\Exceptions\PromiseRejection;
use Sigmie\PollOps\Exceptions\UnknownPromiseResponse;

abstract class AbstractOperation implements PromiseInterface
{
    /**
     * Promise successor
     *
     * @var PromiseInterface|null
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
     * @param array<array-key, mixed> $args
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
     * @param array<array-key, mixed> $args
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
