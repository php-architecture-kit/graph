<?php

declare(strict_types=1);

namespace PhpArchitecture\Graph\Events\Listener;

use PhpArchitecture\Graph\Vertex\VertexInterface;

interface OnVertexRemovedInterface
{
    public function onVertexRemoved(VertexInterface $vertex): void;
}
