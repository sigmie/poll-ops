<?php

declare(strict_types=1);

namespace Sigmie\Promises;

use Closure;

class Pending
{
    private Closure $catch;

    private array $params;

    private AbstractPromise $promise;

    private static $sleep = 'sleep';

    public function __construct(array $params, $catch, $promise)
    {
        $this->params = $params;
        $this->catch = $catch;
        $this->promise = $promise;
    }

    public static function setSleep(callable $sleep)
    {
        self::$sleep = $sleep;
    }

    public function settle()
    {
        if ($this->fulfilled()) {
            return new Fulfilled($this->params);
        }

        ($this->catch)('Unable to verify promise fullifilment');

        return new Settled;
    }

    private function fulfilled()
    {
        $attempts = 0;

        while ($attempts < $this->promise->maxAttempts()) {;
            if ($this->promise->verify()) {
                return true;
            }

            $attempts++;
            call_user_func(self::$sleep, $this->promise->attemptsInterval());
        }

        return false;
    }
}
