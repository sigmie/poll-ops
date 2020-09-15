<?php

declare(strict_types=1);

namespace Sigmie\PollOps\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Sigmie\PollOps\States\Fulfilled;

class FulfilledTest extends TestCase
{
    /**
     * @test
     */
    public function params_method()
    {
        $fulfilled = new Fulfilled(['param']);

        $this->assertEquals(['param'], $fulfilled->params());
    }
}
