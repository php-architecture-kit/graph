<?php

declare(strict_types=1);

namespace PhpArchitecture\Graph\Edge;

use PhpArchitecture\Graph\GraphTrait;
use PhpArchitecture\Graph\Edge\Identity\EdgeId;
use PhpArchitecture\Graph\EdgeWeight\EdgeWeightStore;
use PhpArchitecture\Graph\EdgeWeight\EdgeWeights;
use PhpArchitecture\Graph\Index\IncidenceIndex;
use PhpArchitecture\Graph\Vertex\Exception\VertexNotInGraphException;
use PhpArchitecture\Graph\Vertex\Identity\VertexId;

class EdgeStore
{
    use GraphTrait;

    /**
     * @param array<string, Edge> $store
     */
    public function __construct(
        private array $store = [],
        private readonly ?EdgeWeightStore $weightStore = null
    ) {}

    public function hasWeightStore(): bool
    {
        return $this->weightStore !== null;
    }

    public function addEdge(Edge $edge, ?EdgeWeights $edgeWeights = null): void
    {
        if (isset($this->store[$edge->id->toString()])) {
            throw new Exception\EdgeAlreadyExistsException('Edge `' . $edge->id->toString() . '` already exists');
        }

        $graph = $this->graph();
        $vertexStore = $graph->vertexStore;

        if (!$vertexStore->hasVertex($edge->u())) {
            throw new VertexNotInGraphException('Vertex `' . $edge->u()->toString() . '` is not in graph');
        }
        if (!$vertexStore->hasVertex($edge->v())) {
            throw new VertexNotInGraphException('Vertex `' . $edge->v()->toString() . '` is not in graph');
        }

        $graph->edgeValidator->validate($edge, $graph);

        $this->weightStore?->addEdgeWeights(
            edge: $edge,
            edgeWeights: $edgeWeights ?? new EdgeWeights($edge->id, []),
        );

        $this->store[$edge->id->toString()] = $edge;
        $graph->indexNotifier->notifyEdgeAdded($edge);
    }

    public function getEdgeWeights(EdgeId $id): EdgeWeights
    {
        if ($this->weightStore === null) {
            throw new Exception\MissingEdgeWeightStoreException('Edge weight store is not set. You should set it during graph creation in the GraphConfig.');
        }

        /** @var Edge $edge */
        $edge = $this->getEdge(id: $id, throwException: true);

        return $this->weightStore->edgeWeights($edge->id);
    }

    public function areAdjacent(VertexId $u, VertexId $v): bool
    {
        $incidence = $this->graph()->indexRegistry->index(IncidenceIndex::class);
        if ($incidence !== null) {
            foreach ($incidence->edgesFor($u) as $edge) {
                if ($edge->u()->equals($v) || $edge->v()->equals($v)) {
                    return true;
                }
            }

            return false;
        }

        foreach ($this->store as $edge) {
            if (($edge->u()->equals($u) && $edge->v()->equals($v)) ||
                ($edge->u()->equals($v) && $edge->v()->equals($u))
            ) {
                return true;
            }
        }

        return false;
    }

    public function count(): int
    {
        return count($this->store);
    }

    public function degree(VertexId $vertex): int
    {
        $incidence = $this->graph()->indexRegistry->index(IncidenceIndex::class);
        if ($incidence !== null) {
            return $incidence->degree($vertex);
        }

        return count($this->incidentEdges($vertex));
    }

    /**
     * @param ?callable(Edge):bool $filter
     * @return array<string,Edge>
     */
    public function getAllEdges(?callable $filter = null): array
    {
        return $filter
            ? array_filter($this->store, $filter)
            : $this->store;
    }

    public function getEdge(EdgeId $id, bool $throwException = false): ?Edge
    {
        if ($throwException && !isset($this->store[$id->toString()])) {
            throw new Exception\EdgeNotFoundException('Edge `' . $id->toString() . '` not found');
        }

        return $this->store[$id->toString()] ?? null;
    }

    public function hasEdge(EdgeId $id): bool
    {
        return isset($this->store[$id->toString()]);
    }

    /**
     * @return array<string,Edge>
     */
    public function incidentEdges(VertexId $vertex): array
    {
        $incidence = $this->graph()->indexRegistry->index(IncidenceIndex::class);
        if ($incidence !== null) {
            return $incidence->edgesFor($vertex);
        }

        return array_filter(
            $this->store,
            static fn(Edge $edge): bool => $edge->u()->equals($vertex) || $edge->v()->equals($vertex),
        );
    }

    public function removeEdge(EdgeId $id): void
    {
        if (!isset($this->store[$id->toString()])) {
            throw new Exception\EdgeNotFoundException('Edge `' . $id->toString() . '` not found');
        }

        $edge = $this->store[$id->toString()];
        unset($this->store[$id->toString()]);
        $this->weightStore?->removeEdgeWeights($id);
        $this->graph()->indexNotifier->notifyEdgeRemoved($edge);
    }

    public function populateEdgeDefaultWeights(): void
    {
        $this->weightStore?->populateEdgeDefaultWeights($this->store);
    }
}
