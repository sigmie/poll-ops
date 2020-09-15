<?php

declare(strict_types=1);

namespace Sigmie\PollOps\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Sigmie\PollOps\Chain;
use Sigmie\PollOps\Exceptions\UnhandledRejection;
use Sigmie\PollOps\Exceptions\UnknownPromiseResponse;
use Sigmie\PollOps\States\Fulfilled;
use Sigmie\PollOps\States\Pending;
use Sigmie\PollOps\DefaultOperation;
use Sigmie\PollOps\Tests\Fakes\FakeObject;

if (!function_exists('Sigmie\PollOps\Tests\Unit\sleep')) {
    function sleep()
    {
    }
}

class ChainTest extends TestCase
{
    public function setUp(): void
    {
        Pending::setSleep('Sigmie\PollOps\Tests\Unit\sleep');
    }

    /**
     * @test
     */
    public function promise_chaining_throws_exception_without_catch()
    {
        $this->expectExceptionMessage('Unhandled promise rejection');
        $this->expectException(UnhandledRejection::class);

        $promise = new DefaultOperation(fn () => null);
        $promiseMock = $this->createMock(DefaultOperation::class);

        $chain = new Chain([
            $promise,
            $promiseMock
        ]);

        $chain->proceed();
    }

    /**
     * @test
     */
    public function null_promise_rejection_on_null_return()
    {
        $this->expectException(UnknownPromiseResponse::class);
        $this->expectExceptionMessage('Unknown promise response');

        $promise = new DefaultOperation(fn ($args, $resolve, $reject) => null);

        $chain = new Chain([
            $promise
        ]);

        $chain->catch(fn () => null);

        $chain->proceed();
    }

    /**
     * @test
     */
    public function promise_chaining_executes()
    {
        $closure = fn ($args, $resolve, $reject) => $resolve();

        $promise = new DefaultOperation($closure);
        $secondPromise = new DefaultOperation($closure);
        $thirdPromise = new DefaultOperation($closure);

        $promiseMock = $this->createMock(DefaultOperation::class);
        $promiseMock->method('execute')->willReturn(new Fulfilled([]));

        $chain = new Chain([
            $promise,
            $secondPromise,
            $thirdPromise,
            $promiseMock
        ]);

        $chain->catch(fn () => null);

        $promiseMock->expects($this->once())->method('execute');

        $chain->proceed();
    }

    /**
     * @test
     */
    public function promise_chaining_does_not_continue_on_rejection()
    {
        $resolvable = fn ($args, $resolve, $reject) => $resolve();
        $rejectable = fn ($args, $resolve, $reject) => $reject('Something went wrong!');

        $promise = new DefaultOperation($resolvable);
        $secondPromise = new DefaultOperation($resolvable);
        $thirdPromise = new DefaultOperation($rejectable);

        $promiseMock = $this->createMock(DefaultOperation::class);
        $promiseMock->method('execute')->willReturn(new Fulfilled([]));

        $chain = new Chain([
            $promise,
            $secondPromise,
            $thirdPromise,
            $promiseMock
        ]);

        $chain->catch(fn () => null);

        $promiseMock->expects($this->never())->method('execute');

        $chain->proceed();
    }

    /**
     * @test
     */
    public function catch_is_called_on_promise_rejection()
    {
        $fakeObjectMock = $this->createMock(FakeObject::class);
        $resolvable = fn ($args, $resolve, $reject) => $resolve();
        $rejectable = fn ($args, $resolve, $reject) => $reject('Something went wrong!');
        $promise = new DefaultOperation($resolvable);
        $secondPromise = new DefaultOperation($resolvable);
        $thirdPromise = new DefaultOperation($rejectable);

        $promiseMock = $this->createMock(DefaultOperation::class);
        $promiseMock->method('execute')->willReturn(new Fulfilled([]));

        $chain = new Chain([
            $promise,
            $secondPromise,
            $thirdPromise,
            $promiseMock
        ]);

        $chain->catch(fn () => $fakeObjectMock->someMethod());

        $fakeObjectMock->expects($this->once())->method('someMethod');

        $chain->proceed();
    }

    /**
     * @test
     */
    public function reject_on_false_verification()
    {
        $resolvable = fn ($args, $resolve, $reject) => $resolve();

        $promise = new DefaultOperation($resolvable);
        $secondPromise = new DefaultOperation($resolvable, fn () => false);
        $thirdPromise = new DefaultOperation($resolvable);

        $promiseMock = $this->createMock(DefaultOperation::class);
        $promiseMock->method('execute')->willReturn(new Fulfilled([]));

        $chain = new Chain([
            $promise,
            $secondPromise,
            $thirdPromise,
            $promiseMock
        ]);

        $chain->catch(fn () => null);

        $promiseMock->expects($this->never())->method('execute');

        $chain->proceed();
    }
}
