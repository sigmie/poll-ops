<?php

declare(strict_types=1);

namespace Sigmie\PollOps;

use Sigmie\PollOps\Contracts\Operation;
use Sigmie\PollOps\States\Fulfilled;
use Sigmie\PollOps\States\Pending;
use Sigmie\PollOps\States\Rejected;

trait VerifiesOperation
{
    public function verifyOperation(Operation $operation): bool
    {
        $pendingOperation = new Pending([], $operation);

        $operationResult = $pendingOperation->settle();

        if ($operationResult instanceof Rejected) {
            return false;
        }

        if ($operationResult instanceof Fulfilled) {
            return true;
        }
    }
}
