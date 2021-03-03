<?php

declare(strict_types=1);

namespace Sigmie\PollOps\Tests\Unit;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sigmie\PollOps\InsistentOperation;


class OperationTest extends TestCase
{
    /**
     * @var InsistentOperation|MockObject
     */
    private $operation;

    private static $sleepCount = 0;

    public static function increaseSleepCount()
    {
        self::$sleepCount++;
    }

    public function setUp(): void
    {
        parent::setUp();

        self::$sleepCount = 0;

        if (!function_exists('Sigmie\PollOps\Tests\Unit\testSleep')) {
            function testSleep()
            {
                OperationTest::increaseSleepCount();
            };
        }

        $this->operation = $this->createMock(InsistentOperation::class);

        InsistentOperation::setSleep('Sigmie\PollOps\Tests\Unit\testSleep');
    }

    /**
     * @test
     */
    public function insist_uses_max_attempts_and_returns_false_if_run_wasnt_successful(): void
    {
        $this->operation->expects($this->exactly(15))->method('run')->willReturn(false);

        $this->assertFalse($this->operation->proceed());
    }

    /**
     * @test
     */
    public function is_called_for_each_try_and_before_any_attempt()
    {
        $this->assertNull($this->operation->proceed());

        $this->assertEquals(16, self::$sleepCount);
    }

    /**
     * @test
     */
    public function insist_returns_if_succeeded()
    {
        $this->operation->method('run')->willReturn(false, false, true);

        $this->assertTrue($this->operation->proceed());
        $this->assertEquals(3, self::$sleepCount);
    }
}
