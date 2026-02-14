<?php

declare(strict_types=1);

namespace PhpArchitecture\Graph\EdgeWeight;

use PhpArchitecture\Graph\Edge\Edge;
use PhpArchitecture\Graph\Edge\Identity\EdgeId;
use PhpArchitecture\Graph\EdgeWeight\Config\WeightConfig;
use PhpArchitecture\Graph\EdgeWeight\Exception\EdgeWeightsAlreadyExistsException;
use PhpArchitecture\Graph\EdgeWeight\Exception\EdgeWeightsNotFoundException;

final class EdgeWeightStore
{
    /** @var array<string,EdgeWeights> */
    private array $weightsByEdgeId = [];

    public function __construct(
        private readonly WeightConfig $weightConfig,
    ) {}

    public function addEdgeWeights(Edge $edge, EdgeWeights $edgeWeights): void
    {
        $edgeIdString = $edge->id->toString();
        if (isset($this->weightsByEdgeId[$edgeIdString])) {
            throw new EdgeWeightsAlreadyExistsException('Edge weights for edge `' . $edgeIdString . '` already exist.');
        }

        $edgeWeightsToStore = $edgeWeights->withEdgeId($edge->id);
        $edgeWeightsToStore->fillWith($this->weightConfig->default($edge::class)->all(), true);

        $this->weightsByEdgeId[$edgeIdString] = $edgeWeightsToStore;
    }

    public function edgeWeights(EdgeId $edgeId): EdgeWeights
    {
        $edgeIdString = $edgeId->toString();

        if (!isset($this->weightsByEdgeId[$edgeIdString])) {
            throw new EdgeWeightsNotFoundException('Edge weights for edge `' . $edgeIdString . '` not found.');
        }

        return $this->weightsByEdgeId[$edgeIdString];
    }

    public function removeEdgeWeights(EdgeId $edgeId): void
    {
        unset($this->weightsByEdgeId[$edgeId->toString()]);
    }

    /** @param array<string,Edge> $edges */
    public function populateEdgeDefaultWeights(array $edges): void
    {
        foreach ($edges as $edge) {
            $edgeIdString = $edge->id->toString();
            $defaultWeights = $this->weightConfig->default($edge::class);

            if (isset($this->weightsByEdgeId[$edgeIdString])) {
                $this->weightsByEdgeId[$edgeIdString] = $this->weightsByEdgeId[$edgeIdString]
                    ->fillWith($defaultWeights->all(), true);
                continue;
            }

            $this->weightsByEdgeId[$edgeIdString] = $defaultWeights->withEdgeId($edge->id);
        }
    }
}
