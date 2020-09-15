<?php

declare(strict_types=1);

namespace Sigmie\PollOps\Tests\Fakes;

use Closure;
use PHPUnit\Framework\MockObject\MockObject;

trait SleepMockTrait
{
    private string $sleepFunction = 'Sigmie\PollOps\Tests\Fakes\testSleep';

    public function mockSleep()
    {
        if (!function_exists($this->sleepFunction)) {
            function testSleep()
            {
            };
        }
        return;
    }
}
