<?php

declare(strict_types=1);

namespace PhpArchitecture\Graph\Edge;

use PhpArchitecture\Graph\Edge\Identity\EdgeId;
use PhpArchitecture\Graph\Vertex\VertexInterface;
use PhpArchitecture\Graph\Vertex\Identity\VertexId;

class DirectedEdge implements DirectedEdgeInterface
{
    public readonly EdgeId $id;
    public readonly VertexId $tail;
    public readonly VertexId $head;

    /**
     * @param VertexInterface $tail source / parent vertex
     * @param VertexInterface $head target / child vertex
     * @param array<string,mixed> $metadata
     */
    public function __construct(
        VertexInterface $tail,
        VertexInterface $head,
        ?EdgeId $id = null,
        public array $metadata = [],
    ) {
        $this->tail = $tail->id();
        $this->head = $head->id();
        $this->id = $id ?? EdgeId::new();
    }

    public function id(): EdgeId
    {
        return $this->id;
    }

    public function u(): VertexId
    {
        return $this->tail;
    }

    public function v(): VertexId
    {
        return $this->head;
    }
}
