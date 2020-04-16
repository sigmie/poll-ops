<?php

declare(strict_types=1);

namespace Sigmie\Promises;

use Closure;
use Sigmie\Promises\Exceptions\UnhandledRejection;

class Chain
{
    private array $chain = [];

    private ?Closure $catch = null;

    private ?Closure $then = null;

    public function __construct(array $promises = [])
    {
        $this->chain = $promises;
    }

    public function catch(Closure $closure)
    {
        $this->catch = $closure;

        return $this;
    }

    public function proceed($arguments = [])
    {
        if ($this->catch === null) {
            throw new UnhandledRejection();
        }

        $first = null;

        /** @var AbstractPromise $promise */
        foreach ($this->chain as $successor) {
            if ($first === null) {
                $first = $successor;
                continue;
            }

            $first->setSuccessor($successor);
        }

        return $first->handle($arguments, $this->then, $this->catch);
    }
}
