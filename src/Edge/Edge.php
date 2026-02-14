<?php

declare(strict_types=1);

namespace PhpArchitecture\Graph\Edge;

use PhpArchitecture\Graph\Edge\Identity\EdgeId;
use PhpArchitecture\Graph\Vertex\Identity\VertexId;

abstract class Edge
{
    public EdgeId $id;

    /** 
     * @param array<string,mixed> $metadata 
     */
    public function __construct(
        ?EdgeId $id = null,
        public array $metadata = [],
    ) {
        $this->id = $id ?? EdgeId::new();
    }

    abstract public function u(): VertexId;

    abstract public function v(): VertexId;
}
