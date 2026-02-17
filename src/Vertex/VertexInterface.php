<?php

declare(strict_types=1);

namespace PhpArchitecture\Graph\Vertex;

use PhpArchitecture\Graph\Vertex\Identity\VertexId;

interface VertexInterface
{
    public function id(): VertexId;
}
