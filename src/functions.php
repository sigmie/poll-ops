<?php

namespace Sigmie\PollOps;

use Closure;

/**
 * Insist operation
 */
function insist(Closure $closure): InsistentOperation
{
    $op = new InsistentOperation($closure);

    return $op;
}

/**
 * Operation function
 */
function operation($operation): OperationExecutor
{
    $builder = new OperationExecutor($operation);

    return $builder;
}

/**
 * Chain operations function
 */
function chain(array $args)
{
    return new Chain($args);
}
