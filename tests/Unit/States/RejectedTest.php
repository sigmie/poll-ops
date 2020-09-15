<?php

declare(strict_types=1);

namespace Sigmie\PollOps\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Sigmie\PollOps\Exceptions\PromiseRejection;
use Sigmie\PollOps\States\Rejected;
use Sigmie\PollOps\States\Settled;
use Sigmie\PollOps\Tests\Fakes\FakeObject;

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
