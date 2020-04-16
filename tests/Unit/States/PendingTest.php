<?php

declare(strict_types=1);

namespace Sigmie\Promises\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Sigmie\Promises\AbstractPromise;
use Sigmie\Promises\Fulfilled;
use Sigmie\Promises\Pending;
use Sigmie\Promises\Tests\Fakes\FakeObject;


if (!function_exists('Sigmie\Promises\Tests\Unit\sleep')) {
    function sleep()
    {
    }
}

class PendingTest extends TestCase
{
    /**
     * Setup
     */
    public function setUp(): void
    {
        Pending::setSleep('Sigmie\Promises\Tests\Unit\sleep');
    }

    /**
     * @test
     */
    public function settle_calls_promise_verify(): void
    {
        $promiseMock = $this->createMock(AbstractPromise::class);
        $promiseMock->method('verify')->willReturn(true);
        $promiseMock->method('maxAttempts')->willReturn(3);
        $promiseMock->method('attemptsInterval')->willReturn(10);

        $pending = new Pending([], fn () => null, $promiseMock);

        $promiseMock->expects($this->exactly(1))->method('verify');

        $pending->settle();
    }

    /**
     * @test
     */
    public function settle_calls_promise_verify_max_times_before_returning_false(): void
    {
        $fakeObjectMock = $this->createMock(FakeObject::class);
        $promiseMock = $this->createMock(AbstractPromise::class);
        $promiseMock->method('verify')->willReturn(false);
        $promiseMock->method('maxAttempts')->willReturn(3);
        $promiseMock->method('attemptsInterval')->willReturn(10);

        $pending = new Pending([], fn () => $fakeObjectMock->someMethod(), $promiseMock);

        $promiseMock->expects($this->exactly(3))->method('verify');
        $fakeObjectMock->expects($this->once())->method('someMethod');

        $pending->settle();
    }

    /**
     * @test
     */
    public function fulfilled_response_on_verification_success()
    {
        $promiseMock = $this->createMock(AbstractPromise::class);
        $promiseMock->method('verify')->willReturn(true);
        $promiseMock->method('maxAttempts')->willReturn(3);
        $promiseMock->method('attemptsInterval')->willReturn(10);

        $pending = new Pending([], fn () => null, $promiseMock);

        $response = $pending->settle();

        $this->assertInstanceOf(Fulfilled::class, $response);
    }
}
