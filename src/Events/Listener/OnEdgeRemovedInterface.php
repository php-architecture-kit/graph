<?php

declare(strict_types=1);

namespace PhpArchitecture\Graph\Events\Listener;

use PhpArchitecture\Graph\Edge\Edge;

interface OnEdgeRemovedInterface
{
    public function onEdgeRemoved(Edge $edge): void;
}
