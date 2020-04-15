<?php

declare(strict_types=1);

namespace Sigmie\Promises;

use Closure;
use Exception;

class ImplementationTwo extends AbstractPromise
{
    public function execute($args, Closure $resolve, Closure $reject)
    {
        dump($args);
        dump('runned 2');

        return $resolve('oh something better');
        // return $reject(new Exception('Despacito error'));
    }
}
