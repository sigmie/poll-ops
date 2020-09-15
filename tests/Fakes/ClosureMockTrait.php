<?php

declare(strict_types=1);

namespace Sigmie\PollOps\Tests\Fakes;

use Closure;
use PHPUnit\Framework\MockObject\MockObject;

trait ClosureMockTrait
{
    /**
     * @var Closure|MockObject
     */
    private $closureMock;

    /**
     * @var MockObject
     *
     * @method null closure()
     */
    private $callableMock;

    public function closure()
    {
        $this->callableMock = $this->getMockBuilder(\stdClass::class)->addMethods(['closure'])->getMock();

        $this->closureMock = fn (...$args) => $this->callableMock->closure(...$args);
    }

    public function expectClosureCalledOnce()
    {
        $this->callableMock->expects($this->once())
            ->method('closure');
    }

    public function expectClosureCalledTimes(int $times, array $with = [])
    {
        $this->callableMock->expects($this->exactly($times))
            ->method('closure')
            ->with(...$with);
    }

    public function closureWillReturn(...$values)
    {
        $this->callableMock
            ->method('closure')
            ->willReturn(...$values);
    }

    public function expectClosureCalledWith(...$args)
    {
        $this->callableMock->expects($this->once())
            ->method('closure')
            ->with(...$args);
    }
}
