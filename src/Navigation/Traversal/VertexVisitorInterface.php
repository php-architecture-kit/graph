<?php

declare(strict_types=1);

namespace PhpArchitecture\Graph\Navigation\Traversal;

use PhpArchitecture\Graph\Vertex\VertexInterface;

interface VertexVisitorInterface
{
    public function visit(VertexInterface $vertex): VisitResult;
}
