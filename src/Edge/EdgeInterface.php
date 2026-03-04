<?php

declare(strict_types=1);

namespace PhpArchitecture\Graph\Edge;

use PhpArchitecture\Graph\Edge\Identity\EdgeId;
use PhpArchitecture\Graph\Vertex\Identity\VertexId;

interface EdgeInterface
{
    public function id(): EdgeId;

    public function u(): VertexId;

    public function v(): VertexId;

    public function type(): EdgeType;
}
