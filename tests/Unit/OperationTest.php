<?php

declare(strict_types=1);

namespace Sigmie\PollOps\Tests\Unit;

use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Sigmie\PollOps\Exceptions\UnhandledRejection;
use Sigmie\PollOps\InsistentOperation;
use Sigmie\PollOps\OperationExecutor;
use Sigmie\PollOps\Tests\Fakes\ClosureMockTrait;

use function Sigmie\PollOps\operation;

class OperationTest extends TestCase
{
    use ClosureMockTrait;

    /**
     * @var InsistentOperation|MockObject
     */
    private $operation;

    private static $sleepCount = 0;

    private static $sleepParams = [];

    public static function increaseSleepCount()
    {
        self::$sleepCount++;
    }

    public static function addSleepParams(array $newParams)
    {
        self::$sleepParams = array_merge(self::$sleepParams, $newParams);
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->closure();

        self::$sleepCount = 0;

        if (!function_exists('Sigmie\PollOps\Tests\Unit\testSleep')) {
            function testSleep(...$params)
            {
                OperationTest::increaseSleepCount();
                OperationTest::addSleepParams($params);
            };
        }

        $this->operation = $this->createMock(InsistentOperation::class);

        InsistentOperation::setSleep('Sigmie\PollOps\Tests\Unit\testSleep');
        OperationExecutor::setSleep('Sigmie\PollOps\Tests\Unit\testSleep');
    }

    /**
     * @test
     */
    public function test_catch_without_hint()
    {
        $this->expectClosureCalledOnce();

        operation(function () {
            throw new \RuntimeException('Something went wrong!');
        })->catch(function ($e) {
            ($this->closureMock)();
        })->proceed();
    }

    /**
     * @test
     */
    public function test_multiple_catches()
    {
        $this->expectClosureCalledWith('runtime');

        operation(function () {
            throw new \RuntimeException('Something went wrong!');
        })->catch(function (UnhandledRejection $e) {
            ($this->closureMock)('exception');
        })->catch(function (\RuntimeException $e) {
            ($this->closureMock)('runtime');
        })->proceed();
    }

    /**
     * @test
     */
    public function test_exception_catching()
    {
        $this->expectClosureCalledOnce();

        operation(function () {
            throw new \Exception('Something went wrong!');
        })->catch(function (Exception $e) {
            ($this->closureMock)();
        })->proceed();
    }

    /**
     * @test
     */
    public function operation_can_be_delayed()
    {
        operation(fn ($arg) => $this->assertEquals('do something', $arg))
            ->delay(10)
            ->proceed('do something');

        $this->assertEquals(1, self::$sleepCount);
        $this->assertContains(10, self::$sleepParams);
    }

    /**
     * @test
     */
    public function operation_proceed_passes_arguments()
    {
        operation(fn ($arg) => $this->assertEquals('do something', $arg))
            ->proceed('do something');
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
