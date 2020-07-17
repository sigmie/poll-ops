<?php

declare(strict_types=1);

namespace Sigmie\Promises\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Sigmie\Promises\Exceptions\PromiseRejection;
use Sigmie\Promises\States\Rejected;
use Sigmie\Promises\States\Settled;
use Sigmie\Promises\Tests\Fakes\FakeObject;

class RejectedTest extends TestCase
{
    /**
     * @test
     */
    public function reject_call_given_closure()
    {
        $fakeMock = $this->createMock(FakeObject::class);
        $rejected = new Rejected(new PromiseRejection('Something went wrong'));

        $fakeMock->expects($this->once())->method('someMethod')->with(new PromiseRejection('Something went wrong'));

        $rejected->reject(fn ($message) => $fakeMock->someMethod($message));
    }

    /**
     * @test
     */
    public function reject_return_settled_instance()
    {
        $rejected = new Rejected(new PromiseRejection('Something went wrong'));

        $instance = $rejected->reject(fn () => null);

        $this->assertInstanceOf(Settled::class, $instance);
    }
}
