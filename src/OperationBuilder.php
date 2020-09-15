<?php

declare(strict_types=1);

namespace Sigmie\PollOps;

use Closure;
use Sigmie\PollOps\States\Fulfilled;
use Sigmie\PollOps\States\Pending;
use Sigmie\PollOps\States\Rejected;

class OperationBuilder
{
    private Closure $action;

    private Closure $verifyAction;

    private Closure $catch;

    private Closure $then;

    private Closure $finally;

    private ?int $maxAttempts = null;

    private ?int $attemptsInterval = null;

    public function __construct(Closure $action)
    {
        $this->verifyAction = fn () => true;
        $this->catch = fn () => null;
        $this->then = fn () => null;
        $this->finally = fn () => null;

        $this->action = $action;
    }

    public function verify(Closure $action): self
    {
        $this->verifyAction = $action;

        return $this;
    }

    public function maxAttempts(int $attempts): self
    {
        $this->maxAttempts = $attempts;

        return $this;
    }

    public function attempsInterval(int $attempts): self
    {
        $this->attemptsInterval = $attempts;

        return $this;
    }

    public function catch(Closure $catch): self
    {
        $this->catch = $catch;

        return $this;
    }

    public function then(Closure $then): self
    {
        $this->then = $then;

        return $this;
    }

    public function finally(Closure $finally): self
    {
        $this->finally = $finally;

        return $this;
    }

    public function get()
    {
        $operation = new DefaultOperation($this->action, $this->verifyAction);

        if ($this->maxAttempts !== null) {
            $operation->maxAttempts($this->maxAttempts);
        }

        if ($this->attemptsInterval !== null) {
            $operation->attemptsInterval($this->attemptsInterval);
        }

        return $operation;
    }

    public function proceed()
    {
        $operation = $this->get();

        $operation->proceed();

        $pendingOperation = new Pending([], $operation);
        $operationResult =  $pendingOperation->settle();

        if ($operationResult instanceof Rejected) {
            ($this->catch)();
        }

        if ($operationResult instanceof Fulfilled) {
            ($this->then)();
        }

        ($this->finally)();
    }
}
