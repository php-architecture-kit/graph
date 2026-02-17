<?php

declare(strict_types=1);

namespace PhpArchitecture\Graph\Events\Listener;

use PhpArchitecture\Graph\Edge\DirectedEdgeInterface;
use PhpArchitecture\Graph\Edge\UndirectedEdgeInterface;

interface OnEdgeRemovedInterface
{
    public function onEdgeRemoved(DirectedEdgeInterface|UndirectedEdgeInterface $edge): void;
}
