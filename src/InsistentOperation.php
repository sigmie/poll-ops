<?php

declare(strict_types=1);

namespace Sigmie\PollOps;

use Throwable;

class InsistentOperation extends DefaultOperation
{
    use VerifiesOperation;

    /**
     * Max attempt tries
     *
     * @var int
     */
    protected $tries = 15;
    /**
     * Seconds after which the task should
     * be retried
     *
     * @var int
     */
    protected $retryAfter = 3;

    /**
     * Flag if an exception should be thrown
     */
    protected $catchExceptions = false;

    /**
     * Initial delay before attempt to run
     *
     * @var int
     */
    protected $delay = 0;

    /**
     * @var callable
     */
    private static $sleep = 'sleep';

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

    public function run(): bool
    {
        return ($this->execute)();
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

    public function catchExceptions()
    {
        $this->catchExceptions = true;

        return $this;
    }

    public function retryAfter(int $seconds)
    {
        $this->retryAfter = $seconds;

        return $this;
    }

    final public function proceed(): bool
    {
        call_user_func(self::$sleep, $this->delay);

        $result = false;
        $attempts = 0;

        while ($attempts < $this->tries) {
            try {
                $result = $this->run();
            } catch (Throwable $throwable) {
                if ($this->catchExceptions === false) {
                    throw $throwable;
                }
            }

            if ($result) {
                break;
            }

            $attempts++;
            call_user_func(self::$sleep, $this->retryAfter);
        }

        return $result;
    }
}
