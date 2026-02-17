<?php

declare(strict_types=1);

namespace PhpArchitecture\Graph\Index;

use PhpArchitecture\Graph\Edge\DirectedEdgeInterface;
use PhpArchitecture\Graph\Edge\UndirectedEdgeInterface;
use PhpArchitecture\Graph\Events\Listener\OnEdgeAddedInterface;
use PhpArchitecture\Graph\Events\Listener\OnEdgeRemovedInterface;
use PhpArchitecture\Graph\Vertex\Identity\VertexId;

class IncidenceIndex implements OnEdgeAddedInterface, OnEdgeRemovedInterface
{
    /** @var array<string,array<string,DirectedEdgeInterface|UndirectedEdgeInterface>> vertex_id → [edge_id → Edge] */
    private array $index = [];

    public function onEdgeAdded(DirectedEdgeInterface|UndirectedEdgeInterface $edge): void
    {
        $this->index[$edge->u()->toString()][$edge->id()->toString()] = $edge;
        $this->index[$edge->v()->toString()][$edge->id()->toString()] = $edge;
    }

    public function onEdgeRemoved(DirectedEdgeInterface|UndirectedEdgeInterface $edge): void
    {
        unset($this->index[$edge->u()->toString()][$edge->id()->toString()]);
        unset($this->index[$edge->v()->toString()][$edge->id()->toString()]);
    }

    /**
     * @return array<string,DirectedEdgeInterface|UndirectedEdgeInterface>
     */
    public function edgesFor(VertexId $vertex): array
    {
        return $this->index[$vertex->toString()] ?? [];
    }

    public function degree(VertexId $vertex): int
    {
        return count($this->index[$vertex->toString()] ?? []);
    }
}
