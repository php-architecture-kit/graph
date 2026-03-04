<?php

declare(strict_types=1);

namespace PhpArchitecture\Graph\Navigation\Traversal;

use PhpArchitecture\Graph\Edge\EdgeInterface;

interface EdgeVisitorInterface
{
    public function visit(EdgeInterface $edge): VisitResult;
}
