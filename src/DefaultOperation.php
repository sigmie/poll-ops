<?php

declare(strict_types=1);

namespace Sigmie\PollOps;

use Closure;
use Sigmie\PollOps\Contracts\Operation as PromiseInterface;
use Sigmie\PollOps\States\Pending;
use Sigmie\PollOps\States\Rejected;

class DefaultOperation extends AbstractOperation implements PromiseInterface
{
    private int $maxAttempts = 3;

    private int $attemptsInterval = 30;

    /**
     * Verification closure which can also be null
     * if the PollOps doesn't need verification
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

    public function proceed()
    {
        return ($this->execute)();
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
     * Maximum attempts for generic PollOps
     *
     * @return int
     */
    public function maxAttempts(?int $maxAttempts = null): int
    {
        if ($maxAttempts !== null) {
            $this->maxAttempts = $maxAttempts;
        }

        return $this->maxAttempts;
    }

    /**
     * Generic promise attempts interval
     */
    public function attemptsInterval(?int $attemptsInterval = null): int
    {
        if ($attemptsInterval !== null) {
            $this->attemptsInterval = $attemptsInterval;
        }

        return $this->attemptsInterval;
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
