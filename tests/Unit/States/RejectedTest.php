<?php

declare(strict_types=1);

namespace Sigmie\Promises\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Sigmie\Promises\Exceptions\PromiseRejection;
use Sigmie\Promises\Rejected;
use Sigmie\Promises\Settled;
use Sigmie\Promises\Tests\Fakes\FakeObject;

class RejectedTest extends TestCase
{
    /**
     * @test
     */
    public function reject_call_given_closure()
    {
        $fakeMock = $this->createMock(FakeObject::class);
        $rejected = new Rejected(fn ($message) => $fakeMock->someMethod($message), new PromiseRejection('Something went wrong'));

        $fakeMock->expects($this->once())->method('someMethod')->with(new PromiseRejection('Something went wrong'));

        $rejected->reject();
    }

    /**
     * @test
     */
    public function reject_return_settled_instance()
    {
        $rejected = new Rejected(fn () => null, new PromiseRejection('Something went wrong'));

        $instance = $rejected->reject();

        $this->assertInstanceOf(Settled::class, $instance);
    }
}
