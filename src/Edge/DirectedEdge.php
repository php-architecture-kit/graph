<?php

declare(strict_types=1);

namespace PhpArchitecture\Graph\Edge;

use PhpArchitecture\Graph\Edge\Identity\EdgeId;
use PhpArchitecture\Graph\Vertex\Vertex;
use PhpArchitecture\Graph\Vertex\Identity\VertexId;

class DirectedEdge extends Edge
{
    public readonly VertexId $tail;
    public readonly VertexId $head;

    /**
     * @param Vertex $tail source / parent vertex
     * @param Vertex $head target / child vertex
     * @param array<string,mixed> $metadata
     */
    public function __construct(
        Vertex $tail,
        Vertex $head,
        ?EdgeId $id = null,
        array $metadata = [],
    ) {
        $this->tail = $tail->id;
        $this->head = $head->id;
        parent::__construct($id, $metadata);
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
