<?php

declare(strict_types=1);

namespace Sigmie\PollOps\States;

use Sigmie\PollOps\Contracts\Operation;
use Sigmie\PollOps\Exceptions\PromiseRejection;

class Pending
{
    /**
     * Parameters resolved by the promise
     *
     * @var array
     */
    private array $params;

    /**
     * Promise instance
     *
     * @var Operation
     */
    private Operation $operation;

    /**
     * @var callable
     */
    private static $sleep = 'sleep';

    /**
     * Constructor
     *
     * @param array $params
     * @param Operation $operation
     */
    public function __construct(array $params, Operation $operation)
    {
        $this->params = $params;
        $this->operation = $operation;
    }

    /**
     * Static sleep setter for testing purposes
     *
     * @param callable $sleep
     *
     * @return void
     */
    public static function setSleep(callable $sleep): void
    {
        self::$sleep = $sleep;
    }

    /**
     * Settle method deciding if the given promise was
     * fulfilled or not
     *
     * @return Fulfilled|Rejected
     */
    public function settle()
    {
        if ($this->fulfilled()) {
            return new Fulfilled($this->params);
        }

        return new Rejected(new PromiseRejection('Promise verification failed after %s attempts'));
    }

    /**
     * Verify the promise fulfillment after the promise
     * defined delays and return false if the promise
     * verification failed after the maximum attempts
     * were reached
     *
     * @return bool
     */
    private function fulfilled(): bool
    {
        $attempts = 0;

        while ($attempts < $this->operation->maxAttempts()) {
            if ($this->operation->verify()) {
                return true;
            }

            $attempts++;
            call_user_func(self::$sleep, $this->operation->attemptsInterval());
        }

        return false;
    }
}
