<?php

declare(strict_types=1);

namespace Sigmie\PollOps;

use Closure;

class OperationExecutor
{
    use VerifiesOperation;

    private $operation;

    private Closure $verifyAction;

    private Closure $catch;

    private Closure $then;

    private Closure $finally;

    private ?int $maxAttempts = null;

    private ?int $attemptsInterval = null;

    public function __construct($operation)
    {
        $this->operation = $operation;

        $this->verifyAction = fn () => true;
        $this->catch = fn () => null;
        $this->then = fn () => null;
        $this->finally = fn () => null;
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

    public function create()
    {
        $operation = new DefaultOperation($this->operation, $this->verifyAction);

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
        $operation = $this->operation;

        if ($this->operation instanceof Closure) {
            $operation = $this->create();
        }

        $result = $operation->proceed();

        $verified = $this->verifyOperation($operation);

        if ($verified === true) {
            ($this->then)();
        }

        if ($verified === false) {
            ($this->catch)();
        }

        ($this->finally)();

        return $result;
    }
}
