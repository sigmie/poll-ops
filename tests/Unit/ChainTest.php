<?php

declare(strict_types=1);

namespace Sigmie\Promises\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Sigmie\Promises\Chain;
use Sigmie\Promises\Exceptions\UnhandledRejection;
use Sigmie\Promises\Exceptions\UnknownPromiseResponse;
use Sigmie\Promises\Fulfilled;
use Sigmie\Promises\Pending;
use Sigmie\Promises\Promise;
use Sigmie\Promises\Tests\Fakes\FakeObject;

if (!function_exists('Sigmie\Promises\Tests\Unit\sleep')) {
    function sleep()
    {
    }
}

class ChainTest extends TestCase
{
    public function setUp(): void
    {
        Pending::setSleep('Sigmie\Promises\Tests\Unit\sleep');
    }

    /**
     * @test
     */
    public function promise_chaining_throws_exception_without_catch()
    {
        $this->expectExceptionMessage('Unhandled promise rejection');
        $this->expectException(UnhandledRejection::class);

        $promise = new Promise(fn () => null);
        $promiseMock = $this->createMock(Promise::class);

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

        $promise = new Promise(fn ($args, $resolve, $reject) => null);

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

        $promise = new Promise($closure);
        $secondPromise = new Promise($closure);
        $thirdPromise = new Promise($closure);

        $promiseMock = $this->createMock(Promise::class);
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

        $promise = new Promise($resolvable);
        $secondPromise = new Promise($resolvable);
        $thirdPromise = new Promise($rejectable);

        $promiseMock = $this->createMock(Promise::class);
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
        $promise = new Promise($resolvable);
        $secondPromise = new Promise($resolvable);
        $thirdPromise = new Promise($rejectable);

        $promiseMock = $this->createMock(Promise::class);
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

        $promise = new Promise($resolvable);
        $secondPromise = new Promise($resolvable, fn () => false);
        $thirdPromise = new Promise($resolvable);

        $promiseMock = $this->createMock(Promise::class);
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
