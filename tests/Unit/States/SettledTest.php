<?php

declare(strict_types=1);

namespace Sigmie\Promises\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Sigmie\Promises\Settled;

class SettledTest extends TestCase
{
    /**
     * @test
     */
    public function can_be_instantiated()
    {
        $settled = new Settled();

        $this->assertInstanceOf(Settled::class, $settled);
    }
}
