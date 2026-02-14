<?php

declare(strict_types=1);

namespace PhpArchitecture\Graph\Edge;

use PhpArchitecture\Graph\Edge\Identity\EdgeId;
use PhpArchitecture\Graph\Vertex\Vertex;
use PhpArchitecture\Graph\Vertex\Identity\VertexId;

class UndirectedEdge extends Edge
{
    public readonly VertexId $u;
    public readonly VertexId $v;

    /**
     * @param Vertex $u vertex no 1
     * @param Vertex $v vertex no 2
     * @param array<string,mixed> $metadata
     */
    public function __construct(
        Vertex $u,
        Vertex $v,
        ?EdgeId $id = null,
        array $metadata = [],
    ) {
        $this->u = $u->id;
        $this->v = $v->id;
        parent::__construct($id, $metadata);
    }

    public function u(): VertexId
    {
        return $this->u;
    }

    public function v(): VertexId
    {
        return $this->v;
    }
}
