<?php

declare(strict_types=1);

namespace Sigmie\Promises;

use Closure;
use Exception;

class Implementation extends AbstractPromise
{
    public function execute($args, Closure $resolve, Closure $reject)
    {
        dump($args);
        dump('runned 1');
        return $resolve('oh something good');
    }
}
