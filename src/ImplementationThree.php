<?php

declare(strict_types=1);

namespace Sigmie\Promises;

use Closure;

class ImplementationThree extends AbstractPromise
{
    public function execute($args, Closure $resolve, Closure $reject)
    {
        dump($args);
        dump('runned 3');
        return $resolve('oh something best');
    }
}
