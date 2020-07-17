<?php

declare(strict_types=1);

namespace Sigmie\Promises;

use Closure;
use Sigmie\Promises\Contracts\Promise as PromiseInterface;
use Sigmie\Promises\States\Pending;
use Sigmie\Promises\States\Rejected;

class Promise extends AbstractPromise implements PromiseInterface
{
    /**
     * Verification closure which can also be null
     * if the promises doesn't need verification
     *
     * @var null|Closure
     */
    private ?Closure $verify;

    /**
     * Promise execution code
     *
     * @var Closure
     */
    private Closure $execute;

    /**
     * Constructor
     *
     * @param Closure $execute
     * @param Closure|null $verify
     */
    public function __construct(Closure $execute, Closure $verify = null)
    {
        $this->execute = $execute;
        $this->verify = $verify;
    }

    /**
     * Generic promise
     * @param mixed $args
     * @param Closure $resolve
     * @param Closure $reject
     *
     * @return mixed|Pending|Rejected
     */
    public function execute($args, Closure $resolve, Closure $reject)
    {
        return ($this->execute)($args, $resolve, $reject);
    }

    /**
     * Promise verification method
     *
     * @return bool
     */
    public function verify(): bool
    {
        if ($this->verify === null) {
            return true;
        }

        return ($this->verify)();
    }

    /**
     * Maximum attempts for generic promises
     *
     * @return int
     */
    public function maxAttempts(): int
    {
        return 3;
    }

    /**
     * Generic promise attempts interval
     *
     * @return int
     */
    public function attemptsInterval(): int
    {
        return 30;
    }

    /**
     * Generic promise exception message
     *
     * @return string
     */
    public function exceptionMessage(): string
    {
        return 'Promise fulfilment couldn\'t be verified';
    }
}
