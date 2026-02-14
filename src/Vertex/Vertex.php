<?php

declare(strict_types=1);

namespace PhpArchitecture\Graph\Vertex;

use PhpArchitecture\Graph\Vertex\Identity\VertexId;

class Vertex
{
    public VertexId $id;

    /** @param array<string,mixed> $metadata */
    public function __construct(
        ?VertexId $id = null,
        public array $metadata = [],
    ) {
        $this->id = $id ?? VertexId::new();
    }
}
