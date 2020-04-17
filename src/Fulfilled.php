<?php

declare(strict_types=1);

namespace Sigmie\Promises;

class Fulfilled
{
    /**
     * Promise resolve arguments
     *
     * @var array
     */
    private array $params;

    /**
     * @param mixed $params
     */
    public function __construct($params)
    {
        $this->params = $params;
    }

    /**
     * Resolve arguments
     *
     * @return array
     */
    public function params()
    {
        return $this->params;
    }
}
