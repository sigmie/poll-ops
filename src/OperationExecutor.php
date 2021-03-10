<?php

declare(strict_types=1);

namespace Sigmie\PollOps;

use Closure;
use ReflectionFunction;
use Sigmie\PollOps\Contracts\Operation;
use Sigmie\PollOps\States\Fulfilled;
use Throwable;

class OperationExecutor
{
    use VerifiesOperation;

    private $operation;

    private int $delay = 0;

    private Closure $verifyAction;

    private array $catch;

    private Closure $then;

    private Closure $finally;

    private static $sleep = 'sleep';

    private ?int $maxAttempts = null;

    private ?int $attemptsInterval = null;

    public static function setSleep(callable $sleep): void
    {
        self::$sleep = $sleep;
    }

    public function __construct($operation)
    {
        $this->operation = $operation;

        $this->verifyAction = fn () => true;
        $this->catch = [];
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

    public function attemptsInterval(int $attempts): self
    {
        $this->attemptsInterval = $attempts;

        return $this;
    }

    public function catch(Closure $catch): self
    {
        $this->catch[] = $catch;

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

    private function handleThrowable(Throwable $e)
    {
        foreach ($this->catch as $catch) {
            if ($this->hasThrowableType($catch, $e)) {

                $catch($e);

                return $e;
            }
        }

        throw $e;
    }

    private function handleOperationInstance($args)
    {
        if ($this->operation instanceof InsistentOperation) {
            $this->operation->delay($this->delay);
        }

        $this->operation->setSuccessor(new DefaultOperation(fn () => $this->callThen()));

        try {
            $catch = function ($e) {
                $this->handleThrowable($e);
            };
            $res = $this->operation->handle($args, $catch);
        } catch (Throwable $e) {

            $this->handleThrowable($e);
        }
    }

    public function delay($delay)
    {
        $this->delay = $delay;

        return $this;
    }

    private function callThen()
    {
        ($this->then)();

        return new Fulfilled([]);
    }

    private function hasThrowableType(Closure $fn, Throwable $e)
    {
        $ref = new ReflectionFunction($fn);

        $params = $ref->getParameters();

        if (count($params) === 0) {
            return true;
        }

        [$throwable,] = $params;
        $typeHint = $throwable->getType()?->getName();

        if (is_null($typeHint)) {
            return true;
        }

        return is_subclass_of($e, $typeHint, false) || $e instanceof $typeHint;
    }

    private function handleClosureOperation(...$args)
    {
        $operation = $this->create();

        try {
            call_user_func(self::$sleep, $this->delay);

            $operation->proceed(...$args);

            $verified = $this->verifyOperation($operation);

            if ($verified === true) {
                $this->callThen();
            }

            if ($verified === false) {
                ($this->catch)();
            }
        } catch (Throwable $e) {
            $this->handleThrowable($e);
        }
    }

    public function proceed(...$args): mixed
    {
        if ($this->operation instanceof Closure) {
            $result = $this->handleClosureOperation(...$args);
        } elseif ($this->operation instanceof Operation) {
            $result = $this->handleOperationInstance($args);
        }

        ($this->finally)();

        return $result;
    }
}
