<?php

declare(strict_types=1);

namespace PhpArchitecture\Graph\Events\Listener;

use PhpArchitecture\Graph\Edge\Edge;

interface OnEdgeAddedInterface
{
    public function onEdgeAdded(Edge $edge): void;
}
