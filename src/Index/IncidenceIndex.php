<?php

declare(strict_types=1);

namespace PhpArchitecture\Graph\Index;

use PhpArchitecture\Graph\Edge\EdgeInterface;
use PhpArchitecture\Graph\Events\Listener\OnEdgeAddedInterface;
use PhpArchitecture\Graph\Events\Listener\OnEdgeRemovedInterface;
use PhpArchitecture\Graph\Vertex\Identity\VertexId;

class IncidenceIndex implements OnEdgeAddedInterface, OnEdgeRemovedInterface
{
    /** @var array<string,array<string,EdgeInterface>> vertex_id → [edge_id → Edge] */
    private array $index = [];

    public function onEdgeAdded(EdgeInterface $edge): void
    {
        $this->index[$edge->u()->toString()][$edge->id()->toString()] = $edge;
        $this->index[$edge->v()->toString()][$edge->id()->toString()] = $edge;
    }

    public function onEdgeRemoved(EdgeInterface $edge): void
    {
        unset($this->index[$edge->u()->toString()][$edge->id()->toString()]);
        unset($this->index[$edge->v()->toString()][$edge->id()->toString()]);
    }

    /**
     * @return array<string,EdgeInterface>
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
