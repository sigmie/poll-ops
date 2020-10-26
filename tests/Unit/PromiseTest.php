<?php

declare(strict_types=1);

namespace Sigmie\PollOps\Tests\Unit;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sigmie\PollOps\DefaultOperation;
use Sigmie\PollOps\Tests\Fakes\FakeObject;

class PromiseTest extends TestCase
{
    /**
     * @var MockObject|FakeObject
     */
    private $mock;

    /**
     * @var DefaultOperation
     */
    private $promise;

    /**
     * Setup
     */
    public function setUp(): void
    {
        $this->mock = $this->createMock(FakeObject::class);
        $this->promise = new DefaultOperation(fn () => $this->mock->someMethod());
    }

    /**
     * @test
     */
    public function promise_attempts_intervals()
    {
        $this->assertEquals(30, $this->promise->attemptsInterval());
    }

    /**
     * @test
     */
    public function promise_max_attempts()
    {
        $this->assertEquals(3, $this->promise->maxAttempts());
    }

    /**
     * @test
     */
    public function promise_exception_message()
    {
        $this->assertEquals('Operation fulfilment couldn\'t be verified', $this->promise->exceptionMessage());
    }

    /**
     * @test
     */
    public function execute_calls_given_closure()
    {
        $this->mock->expects($this->once())->method('someMethod');

        $this->promise->execute([], fn () => null, fn () => null);
    }

    /**
     * @test
     */
    public function verify_returns_true_if_not_verification_callback_passed()
    {
        $this->assertTrue($this->promise->verify());
    }

    /**
     * @test
     */
    public function verify_returns_passed_callback_return()
    {
        $promise = new DefaultOperation(fn () => $this->mock->someMethod(), fn () => $this->mock->anotherMethod());

        $this->mock->method('anotherMethod')->willReturn(false);

        $this->assertFalse($promise->verify());
    }
}
