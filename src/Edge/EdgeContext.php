<?php

declare(strict_types=1);

namespace PhpArchitecture\Graph\Edge;

use PhpArchitecture\Graph\Edge\DirectedEdgeInterface;
use PhpArchitecture\Graph\Edge\UndirectedEdgeInterface;
use PhpArchitecture\Graph\EdgeWeight\EdgeWeights;
use PhpArchitecture\Graph\Graph;
use PhpArchitecture\Graph\Vertex\VertexInterface;

class EdgeContext
{
    public function __construct(
        public readonly Graph $graph,
        public readonly DirectedEdgeInterface|UndirectedEdgeInterface $edge,
    ) {}

    public function isDirected(): bool
    {
        return $this->edge instanceof DirectedEdgeInterface;
    }

    public function isUndirected(): bool
    {
        return $this->edge instanceof UndirectedEdgeInterface;
    }

    public function u(): VertexInterface
    {
        /** @phpstan-ignore-next-line */
        return $this->graph->vertexStore->getVertex($this->edge->u(), true);
    }

    public function v(): VertexInterface
    {
        /** @phpstan-ignore-next-line */
        return $this->graph->vertexStore->getVertex($this->edge->v(), true);
    }

    public function weights(): EdgeWeights
    {
        return $this->graph->edgeStore->getEdgeWeights($this->edge->id());
    }
}
