<?php

declare(strict_types=1);

namespace Sigmie\PollOps\Tests\Fakes;

use Closure;
use Sigmie\PollOps\AbstractOperation;

class FakeOperation extends AbstractOperation
{
    private Closure $closure;

    private bool $verifyResult;

    public function __construct(Closure $closure, bool $verifyResult = true)
    {
        $this->closure = $closure;
        $this->verifyResult = $verifyResult;
    }

    public function execute(array $args, Closure $resolve, Closure $reject)
    {
        ($this->closure)();

        return $resolve();
    }

    public function verify(): bool
    {
        return $this->verifyResult;
    }

    public function maxAttempts(): int
    {
        return 10;
    }

    public function attemptsInterval(): int
    {
        return 30;
    }
    public function exceptionMessage(): string
    {
        return 'Something went wrong in the Fake Operation.';
    }
}
