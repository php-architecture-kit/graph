<?php

declare(strict_types=1);

namespace PhpArchitecture\Graph\Navigation\Traversal;

class VisitResult
{
    public function __construct(
        public readonly VisitAction $action,
    ) {}
}
