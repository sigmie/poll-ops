<?php

declare(strict_types=1);

namespace Sigmie\PollOps;

use Closure;

class InsistentOperation
{
    /**
     * Max attempt tries
     *
     * @var int
     */
    protected $tries = 30;
    /**

     * Seconds after which the task should
     * be retried
     *
     * @var int
     */
    protected $retryAfter = 15;

    /**
     * Initial delay before attempt to run
     *
     * @var int
     */
    protected $delay = 90;

    /**
     * @var callable
     */
    private static $sleep = 'sleep';

    protected Closure $closure;

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

    public function __construct(Closure $closure)
    {
        $this->closure = $closure;
    }

    public function run(): bool
    {
        return ($this->closure)();
    }

    public function delay(int $seconds)
    {
        $this->delay = $seconds;

        return $this;
    }

    public function tries(int $tries): self
    {
        $this->tries = $tries;

        return $this;
    }

    /**
     * Insistence code
     *
     * @return bool
     */
    final public function proceed(): bool
    {
        call_user_func(self::$sleep, $this->delay);

        $result = false;
        $attempts = 0;

        while ($attempts < $this->tries) {
            $result = $this->run();
            if ($result) {
                break;
            }

            $attempts++;
            call_user_func(self::$sleep, $this->retryAfter);
        }

        return $result;
    }
}
