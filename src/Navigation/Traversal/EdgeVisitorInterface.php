<?php

declare(strict_types=1);

namespace PhpArchitecture\Graph\Navigation\Traversal;

use PhpArchitecture\Graph\Edge\DirectedEdgeInterface;
use PhpArchitecture\Graph\Edge\UndirectedEdgeInterface;

interface EdgeVisitorInterface
{
    public function visit(DirectedEdgeInterface|UndirectedEdgeInterface $edge): VisitResult;
}
