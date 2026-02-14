<?php

declare(strict_types=1);

namespace PhpArchitecture\Graph;

use PhpArchitecture\Graph\EdgeWeight\Config\WeightConfig;

final readonly class GraphConfig
{
    public function __construct(
        public bool $allowSelfLoop = true,
        public bool $allowMultiEdge = true,
        public bool $allowCyclicEdge = true,
        public ?WeightConfig $weightConfig = null,
    ) {
    }

    public function usesEdgeWeights(): bool
    {
        return $this->weightConfig !== null;
    }
}
