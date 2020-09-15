<?php

declare(strict_types=1);

namespace Sigmie\PollOps\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Sigmie\PollOps\States\Settled;

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
