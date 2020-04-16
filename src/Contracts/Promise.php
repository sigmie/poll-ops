<?php

declare(strict_types=1);

namespace Sigmie\Promises\Contracts;

use Closure;
use Sigmie\Promises\Pending;
use Sigmie\Promises\Rejected;

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

    public function handle($args = null, $then, Closure $catch);

    public function verify(): bool;

    public function maxAttempts(): int;

    public function attemptsInterval(): int;

    public function exceptionMessage(): string;

    // public function then();

    // public function catch();
}
