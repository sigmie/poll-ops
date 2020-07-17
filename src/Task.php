<?php

declare(strict_types=1);

namespace Sigmie\Promises;

abstract class Task
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
    public $delay = 90;

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

    /**
     * Method which will be insisted until
     * it returns true
     *
     * @return bool
     */
    abstract public function run(): bool;

    /**
     * Insistence code
     *
     * @return bool
     */
    final public function insist(): bool
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
