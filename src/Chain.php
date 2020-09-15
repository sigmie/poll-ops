<?php

declare(strict_types=1);

namespace Sigmie\PollOps;

use Closure;
use Sigmie\PollOps\Exceptions\UnhandledRejection;
use Sigmie\PollOps\States\Settled;

class Chain
{
    /**
     * Chain values
     *
     * @var array
     */
    private array $chain = [];

    /**
     * Catch closure
     *
     * @var null|Closure
     */
    private ?Closure $catch = null;

    /**
     * @param array<DefaultOperation> $PollOps
     */
    public function __construct(array $PollOps = [])
    {
        $this->chain = $PollOps;
    }

    /**
     * Promise rejection catch
     *
     * @param Closure $closure
     *
     * @return self
     */
    public function catch(Closure $closure): self
    {
        $this->catch = $closure;

        return $this;
    }

    /**
     * Proceed with the chain fulfillment
     *
     * @param array $arguments
     *
     * @return Settled
     *
     * @throws UnhandledRejection
     */
    public function proceed($arguments = []): Settled
    {
        if ($this->catch === null) {
            throw new UnhandledRejection();
        }

        $first = null;

        foreach ($this->chain as $successor) {
            if ($first === null) {
                $first = $successor;
                continue;
            }

            $first->setSuccessor($successor);
        }

        return $first->handle($arguments, $this->catch);
    }
}
