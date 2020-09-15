<?php

declare(strict_types=1);

namespace Sigmie\PollOps\Contracts;

use Closure;
use Sigmie\PollOps\States\Pending;
use Sigmie\PollOps\States\Rejected;
use Sigmie\PollOps\States\Settled;

interface Operation
{
    /**
     * Setter for the successor promise
     *
     * @param Operation $successor
     *
     * @return void
     */
    public function setSuccessor(Operation $successor): void;

    /**
     * Promise execution
     *
     * @param array $args
     * @param Closure $resolve
     * @param Closure $reject
     *
     * @return Pending|Rejected
     */
    public function execute(array $args, Closure $resolve, Closure $reject);

    /**
     * Promise handle
     *
     * @param array $args
     * @param Closure $catch
     *
     * @return Settled
     */
    public function handle(array $args, Closure $catch): Settled;

    /**
     * Promise fulfillment verification
     *
     * @return bool
     */
    public function verify(): bool;

    /**
     * Max promise verification attempts
     *
     * @return int
     */
    public function maxAttempts(): int;

    /**
     * Promise verification attempts interval
     *
     * @return int
     */
    public function attemptsInterval(): int;

    /**
     * Promise verification exception message
     *
     * @return string
     */
    public function exceptionMessage(): string;
}
