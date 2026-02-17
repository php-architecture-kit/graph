<?php

declare(strict_types=1);

namespace PhpArchitecture\Graph\Events\Listener;

use PhpArchitecture\Graph\Edge\DirectedEdgeInterface;
use PhpArchitecture\Graph\Edge\UndirectedEdgeInterface;

interface OnEdgeAddedInterface
{
    public function onEdgeAdded(DirectedEdgeInterface|UndirectedEdgeInterface $edge): void;
}
