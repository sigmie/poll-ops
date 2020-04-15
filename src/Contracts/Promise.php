<?php

declare(strict_types=1);

namespace Sigmie\Promises\Contracts;

use Closure;

interface Promise
{
    public function setSuccessor(Promise $successor);

    public function execute($args , Closure $resolve, Closure $reject);

    public function handle($args);

    // public function then();

    // public function catch();
}
