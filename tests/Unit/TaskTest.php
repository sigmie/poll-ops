<?php

declare(strict_types=1);

namespace Sigmie\Promises\Tests\Unit;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sigmie\Promises\Task;


class TaskTest extends TestCase
{
    /**
     * @var Task|MockObject
     */
    private $task;

    private static $sleepCount = 0;

    public static function increaseSleepCount()
    {
        self::$sleepCount++;
    }

    public function setUp(): void
    {
        parent::setUp();

        self::$sleepCount = 0;

        if (!function_exists('Sigmie\Promises\Tests\Unit\testSleep')) {
            function testSleep()
            {
                TaskTest::increaseSleepCount();
            };
        }

        $this->task = $this->getMockBuilder(Task::class)->setMethods(['run'])->getMockForAbstractClass();

        Task::setSleep('Sigmie\Promises\Tests\Unit\testSleep');
    }

    /**
     * @test
     */
    public function insist_uses_max_attempts_and_returns_false_if_run_wasnt_successful(): void
    {
        $this->task->expects($this->exactly(30))->method('run')->willReturn(false);

        $this->assertFalse($this->task->insist());
    }

    /**
     * @test
     */
    public function is_called_for_each_try_and_before_any_attempt()
    {
        $this->assertFalse($this->task->insist());

        $this->assertEquals(31, self::$sleepCount);
    }

    /**
     * @test
     */
    public function insist_returns_if_succeeded()
    {
        $this->task->method('run')->willReturn(false, false, true);

        $this->assertTrue($this->task->insist());
        $this->assertEquals(3, self::$sleepCount);
    }
}
