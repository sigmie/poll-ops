<?php

declare(strict_types=1);

namespace Sigmie\Promises;

class Fulfilled
{
    private array $params;

    public function __construct($params)
    {
        $this->params = $params;
    }

    public function params()
    {
        return $this->params;
    }
}
