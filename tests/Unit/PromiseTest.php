<?php

declare(strict_types=1);

namespace Sigmie\Promises\Tests\Unit;

use Exception;
use PHPUnit\Framework\TestCase;
use Sigmie\Promises\Implementation;
use Sigmie\Promises\ImplementationThree;
use Sigmie\Promises\ImplementationTwo;
use Sigmie\Promises\Promise;
use Sigmie\Promises\PromiseAll;

class PromiseTest extends TestCase
{
    // /**
    //  * @test
    //  */
    // public function promise_chaining()
    // {
    //     $promise = new Promise(fn () => null);
    //     $promiseMock1 = $this->createMock(Promise::class);

    //     $promiseMock1->expects($this->once())->method('execute');

    //     $promise->setSuccessor($promiseMock1);

    //     $promise->handle();
    // }

    /**
     * @test
     */
    public function foo()
    {
        $chain = new PromiseAll([
            new Implementation(),
            new ImplementationTwo(),
        ]);

        $chain->catch(fn (Exception $e) => dump($e->getMessage()));
        $chain->then(fn ($value) => dump($value));
        // $chain->then(new ImplementationThree);

        // $chain->then(new ImplementationThree);

        $result = $chain->proceed('fist argument');
        dd('result is' . $result);
    }
}
