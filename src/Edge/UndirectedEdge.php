<?php

declare(strict_types=1);

namespace PhpArchitecture\Graph\Edge;

use PhpArchitecture\Graph\Edge\Identity\EdgeId;
use PhpArchitecture\Graph\Vertex\Identity\VertexId;
use PhpArchitecture\Graph\Vertex\VertexInterface;

class UndirectedEdge implements UndirectedEdgeInterface
{
    public readonly EdgeId $id;
    public readonly VertexId $u;
    public readonly VertexId $v;

    /**
     * @param VertexInterface $u vertex no 1
     * @param VertexInterface $v vertex no 2
     * @param array<string,mixed> $metadata
     */
    public function __construct(
        VertexInterface $u,
        VertexInterface $v,
        ?EdgeId $id = null,
        public array $metadata = [],
    ) {
        $this->u = $u->id();
        $this->v = $v->id();
        $this->id = $id ?? EdgeId::new();
    }
    
    public function id(): EdgeId
    {
        return $this->id;
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
