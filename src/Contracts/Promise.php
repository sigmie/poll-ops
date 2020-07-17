<?php

declare(strict_types=1);

namespace Sigmie\Promises\Contracts;

use Closure;
use Sigmie\Promises\States\Pending;
use Sigmie\Promises\States\Rejected;
use Sigmie\Promises\States\Settled;

interface Promise
{
    /**
     * Setter for the successor promise
     *
     * @param Promise $successor
     *
     * @return void
     */
    public function setSuccessor(Promise $successor): void;

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
